<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use OpenSpout\Reader\Common\Creator\ReaderEntityFactory;


class Students extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Student_model');
    }

    public function index()
    {
        $data['students'] = $this->Student_model->get_all();
        $this->load->view('layout/header');
        $this->load->view('students/index', $data);
        $this->load->view('layout/footer');
    }

    // ✅ Add student
    public function store()
    {
        $data = [
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone')
        ];

        $this->Student_model->insert($data);
        redirect('students');
    }


    public function get_one($id)
    {
        echo json_encode($this->Student_model->get($id));
    }

    public function update_ajax()
    {
        $id = $this->input->post('id');

        $data = [
            'name'  => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone')
        ];

        $this->Student_model->update($id, $data);

        echo json_encode([
            'status' => 'success',
            'message' => 'Student updated successfully'
        ]);
    }

    public function store_ajax()
    {
        $name  = $this->input->post('name');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');

        if (empty($name)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Name is required'
            ]);
            return;
        }

        $data = [
            'name'  => $name,
            'email' => $email,
            'phone' => $phone
        ];

        $id = $this->Student_model->insert($data);

        if ($id) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Student added successfully!',
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Insert failed'
            ]);
        }
    }

    public function get_students()
    {
        $draw   = $this->input->post('draw');
        $start  = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'];

        $this->db->from('students');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }

        $totalFiltered = $this->db->count_all_results('', false);

        $this->db->limit($length, $start);
        $query = $this->db->get();
        $data = $query->result();

        // TOTAL COUNT
        $total = $this->db->count_all('students');

        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ]);
    }

    // ✅ Export Excel
    public function export()
    {

        $students = $this->Student_model->get_all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Phone');

        $row = 2;
        foreach ($students as $s) {
            $sheet->setCellValue('A' . $row, $s->name);
            $sheet->setCellValue('B' . $row, $s->email);
            $sheet->setCellValue('C' . $row, $s->phone);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="students.xlsx"');

        $writer->save('php://output');
    }

    // ✅ Import Excel
    public function import()
    {

        if (!empty($_FILES['file']['name'])) {

            $path = $_FILES['file']['tmp_name'];
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet()->toArray();


            foreach ($sheet as $key => $row) {



                if ($key == 0) continue; // skip header

                $data = [
                    'name' => $row[0],
                    'email' => $row[1],
                    'phone' => $row[2],
                ];

                $this->Student_model->insert($data);
            }
        }

        redirect('students');
    }



    public function export_large()
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToBrowser("students_large.xlsx");

        // HEADER
        $header = WriterEntityFactory::createRowFromArray(['Name', 'Email', 'Phone']);
        $writer->addRow($header);

        // STREAM DATA (IMPORTANT)
        $query = $this->db->get('students');

        foreach ($query->result() as $row) {
            $writer->addRow(
                WriterEntityFactory::createRowFromArray([
                    $row->name,
                    $row->email,
                    $row->phone
                ])
            );
        }

        $writer->close();
    }




    public function import_large_ajax()
    {
        if (empty($_FILES['file']['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'File required']);
            return;
        }

        $filePath = $_FILES['file']['tmp_name'];

        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($filePath);

        $batch = [];
        $count = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {

                if ($index == 1) continue; // skip header

                $cells = $row->toArray();

                $batch[] = [
                    'name'  => $cells[0] ?? '',
                    'email' => $cells[1] ?? '',
                    'phone' => $cells[2] ?? '',
                ];

                $count++;

                if (count($batch) == 500) {
                    $this->db->insert_batch('students', $batch);
                    $batch = [];
                }
            }
        }

        if (!empty($batch)) {
            $this->db->insert_batch('students', $batch);
        }

        $reader->close();

        echo json_encode([
            'status' => 'success',
            'message' => "$count records imported successfully"
        ]);
    }
}
