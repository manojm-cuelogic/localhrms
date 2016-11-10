<?php
class Default_CsiController extends Zend_Controller_Action
{
    public function getemployeedetailsAction()
    {
        $empModel = new Default_Model_Employees();
        $data = $empModel->getEmployees();
        $employees = array();
        foreach($data as $emp) {
            $employees[] = array(
                                "id"                =>  $emp['user_id'],
                                "cue_id"            =>  $emp['employeeId'],
                                "email"             =>  $emp['emailaddress'],
                                "first_name"        =>  $emp['firstname'],
                                "last_name"         =>  $emp['lastname'],
                                "is_active"         =>  $emp['isactive'],
                                "status"            =>  $emp['emp_status_name'],
                                "status_id"         =>  $emp['emp_status_id'],
                                "job_title"         =>  $emp['jobtitle_name'],
                                "job_title_id"      =>  $emp['jobtitle_id'],
                                "role"              =>  $emp['emprole_name'],
                                "role_id"           =>  $emp['emprole'],
                                "date_of_joining"   =>  $emp['date_of_joining'],
                                "date_of_leaving"   =>  $emp['date_of_leaving'],
                                "reporting_manager" =>  array(
                                                            "id"        =>  $emp['reporting_manager'],
                                                            "cue_id"    =>  $emp['m_employeeid'],
                                                            "email"     =>  $emp['m_emailaddress']
                                                        )
                            );
        }
        $successmessage = array("message" => 1, "employees" =>$employees);
        $this->_helper->json($successmessage);
    }

    public function getemployeeleavesAction() {
        $employee_ids = (isset($_POST['employee_ids']) ? $_POST['employee_ids'] : array());
        $year = (isset($_POST['year']) ? $_POST['year'] : 0);
        $month = (isset($_POST['month']) ? $_POST['month'] : array(1));
        $status = (isset($_POST['status']) ? $_POST['status'] : "Approved");


        $empLeaveModel = new Default_Model_Employeeleaves(); //echo "reach"; exit;
        $data = $empLeaveModel->getEmployeeLeaves($employee_ids, $month, $year, $status);
        $successmessage = array("message" => 1, "employees" =>$data);
        $this->_helper->json($successmessage);
    }

    public function getholidaysAction() {
        $empLeaveModel = new Default_Model_Employeeleaves();
        $year = (isset($_POST['year']) ? $_POST['year'] : 0); 
        $data = $empLeaveModel->getCompanyHolidays($year);
        $holidays = array();
        foreach ($data as $value) {
            $holidays[] = array("date" => $value["holidaydate"] , "holiday_name" => $value["holidayname"]);
        }
        $successmessage = array("message" => 1, "holidays" =>$holidays);
        $this->_helper->json($successmessage);
    }
}
?>