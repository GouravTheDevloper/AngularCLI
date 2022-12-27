<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\User;
use App\Models\school;
use App\Models\marksheet_for_exam;
use App\Models\MarksheetFormatSetting;

class MarksheetController extends Controller
{

    public function getmarksheet(){
        // $months = 12;  
        // $years=2016;                                      
        // $monthName = date("F", mktime(0, 0, 0, $months));
        // $fromdt=date('Y-m-01 ',strtotime("First Day Of  $monthName $years")) ;
        // return strtotime($fromdt);
    $studentId = 90539;
    $class_id = 2895;
    $section_id = 3311;
    $marksheetId = 44;
    $school_id = 2;
    if(!empty($school_id)){
        $toReturn = array();
        $date = "2013-5-25";
        $formate_flag = MarksheetFormatSetting::where(array('school_id'=>$school_id , 'status'=> 1,'is_deleted' => 0))->first();
        if(isset($formate_flag) && $formate_flag != Null){
            $toReturn['format_flag'] = 1;
            $toReturn['marksheet_format_id'] = $formate_flag->format_id;
            $toReturn['front_format_id'] = $formate_flag->front_fromat_id;
            $toReturn['back_format_id'] = $formate_flag->back_format_id;
        }else {
            $toReturn['format_flag'] = 0;
        }
        $toReturn['marksheet_year'] = $marksheet_year = marksheet_for_exam::where('id',$marksheetId)->where('school_id',$school_id)->value('year_val');
        $prev_year = $marksheet_year-1;
        $dateArray = array(
            date('Y-m',strtotime($prev_year . '-06-01')),
            date('Y-m',strtotime($prev_year . '-07-01')),
            date('Y-m',strtotime($prev_year . '-08-01')),
            date('Y-m',strtotime($prev_year . '-09-01')),
            date('Y-m',strtotime($prev_year . '-10-01')),
            date('Y-m',strtotime($prev_year . '-11-01')),
            date('Y-m',strtotime($prev_year . '-12-01')),
            date('Y-m',strtotime($marksheet_year . '-01-01')),
            date('Y-m',strtotime($marksheet_year . '-02-01')),
            date('Y-m',strtotime($marksheet_year . '-03-01')),
            date('Y-m',strtotime($marksheet_year . '-04-01')),
            date('Y-m',strtotime($marksheet_year . '-05-01')),
        );
        $total_workingDays = 0;
        $Totalattenddays = 0;
        $total_attendDays = 0;
        $Totalworkingdays =0;
        foreach($dateArray as $key => $date_value){
            $systemMonth = date('m', strtotime($dateArray[$key]));
            $systemYear = date('Y' , strtotime($dateArray[$key]));
            $fromDate = date('Y-m-01', strtotime($dateArray[$key]));
            $toDate = date('Y-m-t', strtotime($dateArray[$key]));
            // return date('n',$counter);
            $workingDays = $this->getWorkingDay($systemMonth,$systemYear,array(0));
            $numberofHoliday = DB::table('holidays')->where('school_id', $school_id)->where('status', 1)->whereBetween('opening_date', [$fromDate,$toDate])->sum('holiday_days');
            if($numberofHoliday != null || $numberofHoliday != ''){
                $total_workingDays = $workingDays-$numberofHoliday;
            }else {
                $total_workingDays = $workingDays;
            }
            $present_day = DB::table('attendance')->whereBetween('attend_date', [$fromDate .'00:00:00' ,$toDate .'23:59:59'])->whereNotIn('status',[0,9])->where('studentId', $studentId)->where('classId',$class_id)->count();
            //total working days array
            $Totalworkingdays = $Totalworkingdays + $total_workingDays;
            $toReturn['workingarray'][$key] = $total_workingDays;
            //total present day array
            $Totalattenddays = $Totalattenddays + $present_day;
            $toReturn['attendArray'][$key] = $present_day;
            
            // sum of totalworking day
            $toReturn['TotalworkingDyas'] = $Totalworkingdays;
            $toReturn['TotalPresentDay'] = $Totalattenddays;
        }
        $toReturn['new'] = date('N',strtotime($date));
        $toReturn['DateFormate'] = date('m',strtotime($date));
        $toReturn['DateFormate1'] = date('n',strtotime($date));
        $toReturn['studentId'] = $studentId;
        $toReturn['marksheetId'] =$marksheetId;
        $toReturn['classId'] = $class_id;
        $toReturn['sectionId'] = $section_id;
        $marksheetDetails = $this->getReportData($studentId);
        $toReturn['studentName'] = $marksheetDetails->fullName;
        $toReturn['stu_address'] = $marksheetDetails->address;
        $toReturn['stu_contact'] = $marksheetDetails->mbileNo;
        $toReturn['stu_addhar'] = $marksheetDetails->aadhaar_no;
        $toReturn['fatherName'] = User::where('school_id',$school_id)->where('parentOf', 'like','%' .$marksheetDetails->id. '%')->where('parentOf', 'like' , '%fat%')->value('fullName');
        $toReturn['MotherName'] = User::where('school_id',$school_id)->where('parentOf', 'like','%' .$marksheetDetails->id. '%')->where('parentOf', 'like' , '%mot%')->value('fullName');
        $toReturn['birthday'] = date('Y-m-d',$marksheetDetails->birthday); // output birthday= 1984-07-16 <= //stringtotime to date birthday = 458850600; 
        $toReturn['admmision_no'] = $marksheetDetails->admission_no;
        $toReturn['hello'] = $marksheetDetails->salutaion;
        

    }else{
        return "NOT SCHOOL ID GET !";
    }
    return $toReturn;
}
public function getReportData($studeId){
    return User::where('id',$studeId)->where('school_id', 2)->first();
}

public static function getWorkingDay($month, $year, $ignore){
    $count = 0;
    $counter = mktime(0, 0, 0, $month, 1, $year);

    while (date("n", $counter) == $month) {
        if (in_array(date("w", $counter), $ignore) == false) {
            $count++;
        }
        $counter = strtotime("+1 day", $counter);
    }

    return $count;
}
}
