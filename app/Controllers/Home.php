<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Error;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

class Home extends BaseController
{
    public function index(): string
    {
        return view('index');
    }

    public function login(): string
    {
        return view('login');
    }

    public function auth()
    {
        $pass = $this->request->getPost('pass');
        $status = 'error';

        if (password_verify($pass, getenv('PASS_HASH'))) {
            session()->set(['logged_in' => true]);
            $status = 'success';
        }

        return json_encode(['status' => $status]);
    }

    public function logout()
    {
        // destroy the session unconditionally
        session()->destroy();

        // return a success response
        return $this->response
            ->setStatusCode(200)
            ->setJSON(['status' => 'success']);
    }

    /**
     * Convert 24-hour “HH:MM” time to 12-hour format with AM/PM.
     *
     * @param string $time24  A time string in “H:i” or “H:i:s” format (e.g. "08:30" or "13:45:00")
     * @return string|false   The formatted time (“hh:mm AM/PM”), or false on parse error
     */
    function format_time(string $time24)
    {
        // Try parsing with or without seconds
        if ($dt = \DateTime::createFromFormat('H:i', $time24)) {
            return $dt->format('h:i') . ' ' . $dt->format('A')[0];
        }
        if ($dt = \DateTime::createFromFormat('H:i:s', $time24)) {
            return $dt->format('h:i') . ' ' . $dt->format('A')[0];
        }

        // If parsing failed, return false
        return false;
    }

    public function clearTemp()
    {
        $dir = WRITEPATH . 'templates' . DIRECTORY_SEPARATOR . 'filled' . DIRECTORY_SEPARATOR;

        if (is_dir($dir)) {
            foreach (glob($dir . '*') as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    public function fill()
    {
        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
        $emp_data = [];
        $data = $this->request->getPost('data');
        $count = $this->request->getPost('count');
        $year = $this->request->getPost('year');
        $month = $this->request->getPost('month');
        // error_log(print_r($data, true) . 'Year: ' . $year . ' Month: ' . $month);

        $emp_data['MONTH'] = $months[$month - 1] . ' ' . $year;
        foreach ($data as $index => $emp) {
            $dtr_index = 0;
            $curr      = null;   // or 0 if you prefer
            $c_index   = ($index === 0 ? 'A' : 'E');
            $emp_details = $this->dtrModel->getEmpName($emp);
            $emp_data['NAME' . ($index + 1)] = $emp_details['emp_name'];
            $condition = [
                'emp_id' => $emp,
                'month' => $month,
                'year' => $year
            ];

            $dtr = $this->dtrModel->provide_emp_dtr($condition);
            // error_log(print_r($dtr, true));

            if (! empty($dtr)) {
                $currDay   = null;

                foreach ($dtr as $d) {
                    if ($currDay !== $d['day']) {
                        $currDay   = $d['day'];
                        $dtr_index = 0;
                        $c_index   = ($index === 0 ? 'A' : 'E');
                    }

                    if ($dtr_index < 4) {
                        $emp_data[$d['day'] . $c_index] = $this->format_time($d['time']);
                        $dtr_index++;
                        $c_index++;
                    }
                }
            }
        }
        // error_log(print_r($emp_data, true));

        // 1) Fill the DOCX
        $templatePath = WRITEPATH . 'templates' . DIRECTORY_SEPARATOR . 'dtr-template-single.docx';
        if (count($data) > 1) {
            $templatePath = WRITEPATH . 'templates' . DIRECTORY_SEPARATOR . 'dtr-template.docx';
        }
        $filledDocx   = WRITEPATH . 'templates' . DIRECTORY_SEPARATOR . 'filled_' . $data[0]
            . (isset($data[1]) ? '_' . $data[1] : '')
            . '.docx';

        $filledPdf = WRITEPATH . 'templates' . DIRECTORY_SEPARATOR
            . 'filled_' . $data[0]
            . (isset($data[1]) ? '_' . $data[1] : '')
            . '.pdf';

        $final_path = WRITEPATH . 'templates' . DIRECTORY_SEPARATOR . 'filled' . DIRECTORY_SEPARATOR;
        if (!is_dir($final_path)) {
            mkdir($final_path, 0775, true); // 0775 permissions and recursive creation
        }

        $tpl = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        foreach ($data as $index => $d) {
            $tpl->setValue('NAME' . ($index + 1), $emp_data['NAME' . ($index + 1)]);
        }

        $tpl->setValue('MONTH', $emp_data['MONTH']);

        for ($i = 1; $i <= 32; $i++) {
            for ($c = 'A'; $c !== 'I'; $c++) {
                if (isset($emp_data[$i . $c]) && !empty($emp_data[$i . $c])) {
                    $tpl->setValue($i . $c, $emp_data[$i . $c]);
                } else {
                    $tpl->setValue($i . $c, '');
                }
            }
        }

        $tpl->saveAs($filledDocx);

        $soffice = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe"';

        // use escapeshellarg so paths with spaces work
        $docxArg  = escapeshellarg($filledDocx);
        $outDir   = escapeshellarg(WRITEPATH . 'templates');

        $cmd = $soffice
            . ' --headless'
            . ' --convert-to pdf'
            . " $docxArg"
            . ' --outdir'
            . " $outDir"
            . ' 2>&1';

        // execute and capture output + return code
        exec($cmd, $outputLines, $returnCode);

        // give LibreOffice a moment
        sleep(1);
        unlink($filledDocx);

        // Rename output
        if (file_exists($filledPdf)) {
            $targetPdf = $final_path . DIRECTORY_SEPARATOR . 'DTR_' . $months[$month - 1] . '_' . $year . '.pdf';

            if ($count == 1) {
                rename($filledPdf, $targetPdf);
            } else {
                $pdf = new Fpdi();

                if (file_exists($targetPdf)) {
                    $pageCount = $pdf->setSourceFile($targetPdf);
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $tplIdx = $pdf->importPage($pageNo);
                        $pdf->AddPage();
                        $pdf->useTemplate($tplIdx);
                    }
                }

                // Add newly generated PDF
                $pageCount = $pdf->setSourceFile($filledPdf);
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $tplIdx = $pdf->importPage($pageNo);
                    $pdf->AddPage();
                    $pdf->useTemplate($tplIdx);
                }

                // Overwrite merged file
                $pdf->Output('F', $targetPdf);

                // Optionally delete the individual PDF
                unlink($filledPdf);
            }
        } else {
            return $this->response
                ->setStatusCode(500)
                ->setBody("PDF generation failed. Check logs for LibreOffice output.");
        }

        return json_encode(['status' => 'success']);
    }

    public function download_pdf()
    {
        $directory = WRITEPATH . 'templates' . DIRECTORY_SEPARATOR . 'filled' . DIRECTORY_SEPARATOR;
        $file_name = basename($this->request->getPost('file')); // prevent directory traversal
        $file_path = $directory . $file_name;

        if (!is_file($file_path)) {
            return $this->response
                ->setStatusCode(404)
                ->setBody('File not found.');
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $file_name . '"')
            ->setHeader('Content-Length', filesize($file_path))
            ->setBody(file_get_contents($file_path));
    }

    private function date_separate($d)
    {
        $date = \DateTime::createFromFormat('n/j/Y', $d);

        if ($date) {
            return [
                "day"   => (int) $date->format('j'), // cast to int
                "month" => (int) $date->format('n'), // cast to int
                "year"  => (int) $date->format('Y')  // cast to int
            ];
        }

        return null;
    }

    public function import()
    {
        $body = $this->request->getBody();
        $payload = json_decode($body, true);

        if (is_array($payload) && !empty($payload)) {
            foreach ($payload as $row) :

                $date = $this->date_separate($row['date']);
                $times = explode(';', $row['punch']);

                if (!is_null($date) && (is_array($times) && !empty($times))) :
                    foreach ($times as $time) :

                        $parameters = [
                            'emp_id' => trim($row['id']),
                            'emp_name' => trim($row['name']),
                            'day' => trim($date['day']),
                            'month' => trim($date['month']),
                            'year' => trim($date['year']),
                            'time' => trim($time),
                        ];

                        if (!$this->dtrModel->check($parameters)) :
                            $this->dtrModel->add($parameters);
                        endif;
                    endforeach;
                endif;
            endforeach;
        }
    }

    public function fetch_id()
    {
        return json_encode($this->dtrModel->provide_ids());
    }

    public function fetch_year()
    {
        return json_encode($this->dtrModel->provide_year());
    }

    public function fetch_month()
    {
        $year = $this->request->getPost('year');
        return json_encode($this->dtrModel->provide_month($year));
    }
}
