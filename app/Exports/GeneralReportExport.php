<?php

namespace App\Exports;

use App\Models\MsProduct;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class GeneralReportExport implements FromCollection, WithHeadings
{
    // use Exportable;
    protected $tglMasuk;
    protected $tglKeluar;
    protected $nipon;
    protected $jenisreport;

    public function __construct($tglMasuk, $tglKeluar,$nipon,$jenisreport)
    {
        $this->tglMasuk = $tglMasuk;
        $this->tglKeluar = $tglKeluar;
        $this->nipon = $nipon;
        $this->jenisreport = $jenisreport;
    }

    public function collection()
    {
        $tglMasuk = '';
        if (isset($this->tglMasuk) && $this->tglMasuk != '') {
            $tglMasuk = "WHERE tod.processdate >= '" . $this->tglMasuk . "'";
        }
        $tglKeluar = '';
        if (isset($this->tglKeluar) && $this->tglKeluar != '') {
            $tglKeluar = "AND tod.processdate <= '" . $this->tglKeluar . "'";
        }

        if ($this->nipon==1){
            switch($this->jenisreport) {
                case '1':
                    return collect(DB::select("
                        SELECT max(mac.machineNo) AS machine_no,
                        max(mac.machineName) AS machine_name,
                        max(dep.name) AS department_name,
                        SUM(asy.berat_standard) AS berat_standard,
                        SUM(asy.berat_produksi) AS berat_produksi,
                        SUM(asy.infure_cost) AS infure_cost,
                        SUM(asy.infure_berat_loss) AS infure_berat_loss,
                        SUM(asy.panjang_produksi) AS panjang_produksi,
                        SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                        SUM(asy.infure_cost_printing) AS infure_cost_printing,
                        COALESCE(MAX(jam.work_hour), 0) AS work_hour_mm,
                        COALESCE(MAX(jam.off_hour), 0) AS work_hour_off_mm,
                        COALESCE(MAX(jam.on_hour), 0) AS work_hour_on_mm 
                    FROM tdProduct_Assembly AS asy
                    LEFT JOIN LATERAL (
                        SELECT 
                            SUM(EXTRACT(EPOCH FROM work_hour) / 60) AS work_hour, 
                            SUM(EXTRACT(EPOCH FROM off_hour) / 60) AS off_hour, 
                            SUM(EXTRACT(EPOCH FROM on_hour) / 60) AS on_hour 
                        FROM tdJamKerjaMesin AS jam_ 
                        WHERE asy.machine_id = jam_.machine_id 
                        AND jam_.working_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY jam_.machine_id
                    ) AS jam ON true
                    left JOIN msMachine AS mac ON asy.machine_id = mac.id 
                    left JOIN msDepartment AS dep ON mac.department_id = dep.id 
                    WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                    GROUP BY asy.machine_id
            
                    "));
                    break;
    
                case '2':
                    return collect(DB::select("
                        select max(dep.name) AS department_name,
                            max(prTip.name) AS product_type_name,
                            max(mac.machineNo) AS machine_no,
                            max(mac.machineName) AS machine_name,
                            SUM(asy.berat_standard) AS berat_standard,
                            SUM(asy.berat_produksi) AS berat_produksi,
                            SUM(asy.infure_cost) AS infure_cost,
                            SUM(asy.infure_berat_loss) AS infure_berat_loss,
                            SUM(asy.panjang_produksi) AS panjang_produksi,
                            SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                            SUM(asy.infure_cost_printing) AS infure_cost_printing
                        FROM tdProduct_Assembly AS asy
                        left JOIN msMachine AS mac ON asy.machine_id = mac.id 
                        left JOIN msDepartment AS dep ON mac.department_id = dep.id 
                        left JOIN msProduct AS prd ON asy.product_id = prd.id 
                        left JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id 
                        WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, asy.machine_id, prTip.id
            
                    "));
                    break;
    
                case '3':
                    return collect(DB::select("
                        SELECT max(prGrp.code) AS product_group_code,
                        max(prGrp.name) AS product_group_name,
                        SUM(asy.berat_standard) AS berat_standard,
                        SUM(asy.berat_produksi) AS berat_produksi,
                        SUM(asy.infure_cost) AS infure_cost,
                        SUM(asy.infure_berat_loss) AS infure_berat_loss,
                        SUM(asy.panjang_produksi) AS panjang_produksi,
                        SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                        SUM(asy.infure_cost_printing) AS infure_cost_printing
                    FROM tdProduct_Assembly AS asy
                    INNER JOIN msProduct AS prd ON asy.product_id = prd.id 
                    INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id 
                    INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id 
                    WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                    GROUP BY prGrp.id
        
                "));
                    break;
                case '4':
                    return collect(DB::select("
                         SELECT 
                            max(prTip.code) AS product_type_code,
                            max(prTip.name) AS product_type_name,
                            SUM(asy.berat_standard) AS berat_standard,
                            SUM(asy.berat_produksi) AS berat_produksi,
                            SUM(asy.infure_cost) AS infure_cost,
                            SUM(asy.infure_berat_loss) AS infure_berat_loss,
                            SUM(asy.panjang_produksi) AS panjang_produksi,
                            SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                            SUM(asy.infure_cost_printing) AS infure_cost_printing
                        FROM tdProduct_Assembly AS asy
                        INNER JOIN msProduct AS prd ON asy.product_id = prd.id 
                        INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id 
                        WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY prTip.id
        
                    "));
                    break;   
                case '5':
                    return collect(DB::select("
                        SELECT 
                            max(prd.code) AS product_code,
                            max(prd.name) AS product_name,
                            SUM(asy.berat_standard) AS berat_standard,
                            SUM(asy.berat_produksi) AS berat_produksi,
                            SUM(asy.infure_cost) AS infure_cost,
                            SUM(asy.infure_berat_loss) AS infure_berat_loss,
                            SUM(asy.panjang_produksi) AS panjang_produksi,
                            SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                            SUM(asy.infure_cost_printing) AS infure_cost_printing
                        FROM tdProduct_Assembly AS asy
                        INNER JOIN msProduct AS prd ON asy.product_id = prd.id  
                        WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY prd.id
        
                "));
                    break;
                case '6':
                    return collect(DB::select("
                        SELECT 
                            max(dep.name) AS department_name,
                            max(prGrp.code) AS product_group_code,
                            max(prGrp.name) AS product_group_name,
                            SUM(asy.berat_standard) AS berat_standard,
                            SUM(asy.berat_produksi) AS berat_produksi,
                            SUM(asy.infure_cost) AS infure_cost,
                            SUM(asy.infure_berat_loss) AS infure_berat_loss,
                            SUM(asy.panjang_produksi) AS panjang_produksi,
                            SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                            SUM(asy.infure_cost_printing) AS infure_cost_printing
                        FROM tdProduct_Assembly AS asy
                        INNER JOIN msMachine AS mac ON asy.machine_id = mac.id 
                        INNER JOIN msDepartment AS dep ON mac.department_id = dep.id 
                        INNER JOIN msProduct AS prd ON asy.product_id = prd.id 
                        INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id 
                        INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id 
                        WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, prGrp.id
        
                    "));
                    break;         
                case '7':
                    return collect(DB::select("
                        SELECT 
                            max(dep.name) AS department_name,
                            max(prTip.code) AS product_type_code,
                            max(prTip.name) AS product_type_name,
                            SUM(asy.berat_standard) AS berat_standard,
                            SUM(asy.berat_produksi) AS berat_produksi,
                            SUM(asy.infure_cost) AS infure_cost,
                            SUM(asy.infure_berat_loss) AS infure_berat_loss,
                            SUM(asy.panjang_produksi) AS panjang_produksi,
                            SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                            SUM(asy.infure_cost_printing) AS infure_cost_printing
                        FROM tdProduct_Assembly AS asy
                        INNER JOIN msMachine AS mac ON asy.machine_id = mac.id 
                        INNER JOIN msDepartment AS dep ON mac.department_id = dep.id 
                        INNER JOIN msProduct AS prd ON asy.product_id = prd.id 
                        INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id 
                        WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, prTip.id
        
                    "));  
                        break; 
                case '8':
                    return collect(DB::select("
                        SELECT 
                            max(dep.name) AS department_name,
                            max(man.employeeNo) AS employeeNo,
                            max(man.empName) AS empName,
                            SUM(asy.berat_standard) AS berat_standard,
                            SUM(asy.berat_produksi) AS berat_produksi,
                            SUM(asy.infure_cost) AS infure_cost,
                            SUM(asy.infure_berat_loss) AS infure_berat_loss,
                            SUM(asy.panjang_produksi) AS panjang_produksi,
                            SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                            SUM(asy.infure_cost_printing) AS infure_cost_printing
                        FROM tdProduct_Assembly AS asy
                        INNER JOIN msEmployee AS man ON asy.employee_id = man.id 
                        INNER JOIN msDepartment AS dep ON man.department_id = dep.id 
                        WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, asy.employee_id
        
                    "));
                        break; 
                // case '9':

                    //     break;    
                case '10':
                    return collect(DB::select("
                        SELECT 
                            max(dep.name) AS department_name,
                            max(mslosCls.name) AS loss_class_name,
                            max(mslos.code) AS loss_code,
                            max(mslos.name) AS loss_name,
                            SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                            SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan
                        FROM tdProduct_Assembly AS asy
                        INNER JOIN tdProduct_Assembly_Loss AS det ON asy.id = det.product_assembly_id
                        INNER JOIN msLossInfure AS mslos ON det.loss_infure_id = mslos.id
                        INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
                        INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
                        INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
                        WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, det.loss_infure_id
        
                    "));
                        break;
                case '11':
                    return collect(DB::select("
                        SELECT 
                            max(dep.name) AS department_name,
                            max(prGrp.code || ' : ' || prGrp.name) AS product_group_name,
                            max(mslosCls.name) AS loss_class_name,
                            max(mslos.code) AS loss_code,
                            max(mslos.name) AS loss_name,
                            SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                            SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan
                        FROM tdProduct_Assembly AS asy
                        INNER JOIN tdProduct_Assembly_Loss AS det ON asy.id = det.product_assembly_id
                        INNER JOIN msLossInfure AS mslos ON det.loss_infure_id = mslos.id
                        INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
                        INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
                        INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
                        INNER JOIN msProduct AS prd ON asy.product_id = prd.id
                        INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
                        INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id
                        WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, prGrp.id, det.loss_infure_id
        
                    "));
                         break;
                case '12':
                    return collect(DB::select("
                        SELECT 
                            max(dep.name) AS department_name,
                            max(mac.employeeNo) AS employeeNo,
                            max(mac.empName) AS empName,
                            SUM(asy.berat_produksi) AS berat_produksi,
                            SUM(asy.infure_berat_loss) AS infure_berat_loss,
                            COALESCE(SUM(loss.berat_loss_01), 0) AS berat_loss_01,
                            COALESCE(SUM(loss.berat_loss_02), 0) AS berat_loss_02,
                            COALESCE(SUM(loss.berat_loss_03), 0) AS berat_loss_03,
                            COALESCE(SUM(loss.berat_loss_04), 0) AS berat_loss_04,
                            COALESCE(SUM(loss.berat_loss_05), 0) AS berat_loss_05,
                            COALESCE(SUM(loss.berat_loss_06), 0) AS berat_loss_06,
                            COALESCE(SUM(loss.berat_loss_07), 0) AS berat_loss_07,
                            COALESCE(SUM(loss.berat_loss_99), 0) AS berat_loss_99,
                            COALESCE(MAX(loss_sitai.infure_berat_loss), 0) AS seitai_infure_berat_loss
                        FROM tdProduct_Assembly AS asy
                        LEFT JOIN LATERAL (
                            SELECT
                                SUM(CASE WHEN mslosCls.code = '01' THEN los_.berat_loss ELSE 0 END) AS berat_loss_01,
                                SUM(CASE WHEN mslosCls.code = '02' THEN los_.berat_loss ELSE 0 END) AS berat_loss_02,
                                SUM(CASE WHEN mslosCls.code = '03' THEN los_.berat_loss ELSE 0 END) AS berat_loss_03,
                                SUM(CASE WHEN mslosCls.code = '04' THEN los_.berat_loss ELSE 0 END) AS berat_loss_04,
                                SUM(CASE WHEN mslosCls.code = '05' THEN los_.berat_loss ELSE 0 END) AS berat_loss_05,
                                SUM(CASE WHEN mslosCls.code = '06' THEN los_.berat_loss ELSE 0 END) AS berat_loss_06,
                                SUM(CASE WHEN mslosCls.code = '07' THEN los_.berat_loss ELSE 0 END) AS berat_loss_07,
                                SUM(CASE WHEN mslosCls.code = '99' THEN los_.berat_loss ELSE 0 END) AS berat_loss_99
                            FROM tdProduct_Assembly_Loss AS los_
                            INNER JOIN msLossInfure AS mslos ON los_.loss_infure_id = mslos.id
                            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
                            WHERE asy.id = los_.product_assembly_id
                            GROUP BY los_.product_assembly_id
                        ) AS loss ON true
                        LEFT JOIN (
                            SELECT 
                                good.employee_id_infure,
                                SUM(good.infure_berat_loss) AS infure_berat_loss
                            FROM tdProduct_Goods AS good
                            WHERE (good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar')
                            GROUP BY good.employee_id_infure
                        ) AS loss_sitai ON asy.employee_id = loss_sitai.employee_id_infure
                        INNER JOIN msEmployee AS mac ON asy.employee_id = mac.id
                        INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
                        WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, asy.employee_id
                     "));    
                        break;            
                case '13':
                    return collect(DB::select("
                         SELECT 
                            max(mac.machineNo || ' : ' || mac.machineName) AS machine_name,
                            max(mslosCls.name) AS loss_class_name,
                            max(mslos.code) AS loss_code,
                            max(mslos.name) AS loss_name,
                            SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                            SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan
                        FROM tdProduct_Assembly AS asy
                        INNER JOIN tdProduct_Assembly_Loss AS det ON asy.id = det.product_assembly_id 
                        INNER JOIN msLossInfure AS mslos ON det.loss_infure_id = mslos.id 
                        INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id 
                        INNER JOIN msMachine AS mac ON asy.machine_id = mac.id 
                        WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY mac.id, det.loss_infure_id;
        
                    "));
                        break;            
                case '14':
                    return collect(DB::select("
                        SELECT 
                            max(prGrp.code) AS product_group_code,
                            max(prGrp.name) AS product_group_name,
                            MAX(mac.machineNo) AS machine_no,
                            MAX(mac.machineName) AS machine_name,
                            SUM(asy.panjang_produksi) AS panjang_produksi,
                            SUM(asy.berat_produksi) AS berat_produksi,
                            MAX(mac.capacity_kg) AS capacity_kg,
                            MAX(mac.capacity_lembar) AS capacity_lembar--,
                            --@day AS seq_no  
                        FROM tdProduct_Assembly AS asy
                        INNER JOIN msProduct AS prd ON asy.product_id = prd.id 
                        INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id 
                        INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id 
                        INNER JOIN msMachine AS mac ON asy.machine_id = mac.id 
                        WHERE asy.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY prGrp.id, asy.machine_id
        
                    "));
                    break;
                    default:
                    
            }
         
         }else{
            switch($this->jenisreport) {
                case '1':
                    return collect(DB::select("
                        SELECT 
                                MAX(mac.machineNo) AS machine_no,
                                MAX(mac.machineName) AS machine_name,
                                MAX(dep.name) AS department_name,
                                SUM(good.qty_produksi) AS qty_produksi,
                                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                                SUM(good.infure_berat_loss) AS infure_berat_loss,
                                COALESCE(MAX(jam.work_hour), 0) AS work_hour_mm,
                                COALESCE(MAX(jam.off_hour), 0) AS work_hour_off_mm,
                                COALESCE(MAX(jam.on_hour), 0) AS work_hour_on_mm
                            FROM tdProduct_Goods AS good 
                            LEFT JOIN (
                                SELECT 
                                    los_.product_goods_id, 
                                    SUM(los_.berat_loss) AS berat_loss
                                FROM tdProduct_Goods_Loss AS los_
                                WHERE los_.loss_seitai_id = 1
                                GROUP BY los_.product_goods_id
                            ) ponsu ON good.id = ponsu.product_goods_id
                            LEFT JOIN (
                                SELECT 
                                    jam_.machine_id,
                                    SUM(EXTRACT(EPOCH FROM work_hour) / 60) AS work_hour,
                                    SUM(EXTRACT(EPOCH FROM off_hour) / 60) AS off_hour,
                                    SUM(EXTRACT(EPOCH FROM on_hour) / 60) AS on_hour
                                FROM tdJamKerjaMesin AS jam_
                                WHERE jam_.working_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                                GROUP BY jam_.machine_id
                            ) jam ON good.machine_id = jam.machine_id
                            INNER JOIN msMachine AS mac ON good.machine_id = mac.id 
                            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id 
                            INNER JOIN msProduct AS prd ON good.product_id = prd.id 
                            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id 
                            WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                            GROUP BY good.machine_id;
            
                    "));
                    break;
    
                case '2':
                    return collect(DB::select("
                       SELECT 
                            MAX(dep.name) AS department_name,
                            MAX(prT.name) AS product_type_name,
                            MAX(mac.machineNo) AS machine_no,
                            MAX(mac.machineName) AS machine_name,							
                            SUM(good.qty_produksi) AS qty_produksi,
                            SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                            SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                            SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                            COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                            SUM(good.infure_berat_loss) AS infure_berat_loss
                        FROM tdProduct_Goods AS good 
                        LEFT JOIN (
                            SELECT 
                                los_.product_goods_id, 
                                SUM(los_.berat_loss) AS berat_loss
                            FROM tdProduct_Goods_Loss AS los_
                            WHERE los_.loss_seitai_id = 1
                            GROUP BY los_.product_goods_id
                        ) ponsu ON good.id = ponsu.product_goods_id
                        INNER JOIN msMachine AS mac ON good.machine_id = mac.id 
                        INNER JOIN msDepartment AS dep ON mac.department_id = dep.id 
                        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
                        INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id 
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, prT.name, good.machine_id;
            
                    "));
                    break;
    
                case '3':
                    return collect(DB::select("
                        SELECT 
                            MAX(prGrp.code) AS product_group_code,
                            MAX(prGrp.name) AS product_group_name,
                            SUM(good.qty_produksi) AS qty_produksi,
                            SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                            SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost, 
                            SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                            COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                            SUM(good.infure_berat_loss) AS infure_berat_loss 
                        FROM tdProduct_Goods AS good 
                        LEFT JOIN (
                            SELECT 
                                los_.product_goods_id, 
                                SUM(los_.berat_loss) AS berat_loss
                            FROM tdProduct_Goods_Loss AS los_
                            WHERE los_.loss_seitai_id = 1 -- ponsu
                            GROUP BY los_.product_goods_id
                        ) ponsu ON good.id = ponsu.product_goods_id
                        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
                        INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id 
                        INNER JOIN msProduct_group AS prGrp ON prT.product_group_id = prGrp.id 
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY prGrp.name;
        
                    "));
                    break;
                case '4':
                    return collect(DB::select("
                       SELECT 
                            MAX(prT.code) AS product_type_code,
                            MAX(prT.name) AS product_type_name,
                            SUM(good.qty_produksi) AS qty_produksi,
                            SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                            SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost, 
                            SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                            COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                            SUM(good.infure_berat_loss) AS infure_berat_loss 
                        FROM tdProduct_Goods AS good 
                        LEFT JOIN (
                            SELECT 
                                los_.product_goods_id, 
                                SUM(los_.berat_loss) AS berat_loss
                            FROM tdProduct_Goods_Loss AS los_
                            WHERE los_.loss_seitai_id = 1 -- ponsu
                            GROUP BY los_.product_goods_id
                        ) ponsu ON good.id = ponsu.product_goods_id
                        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
                        INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id 
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY prT.id;
        
                    "));
                    break;   
                case '5':
                    return collect(DB::select("
                        SELECT 
                            MAX(prd.code) AS product_code,
                            MAX(prd.name) AS product_name,
                            SUM(good.qty_produksi) AS qty_produksi,
                            SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                            SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost, 
                            SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                            COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                            SUM(good.infure_berat_loss) AS infure_berat_loss 
                        FROM tdProduct_Goods AS good 
                        LEFT JOIN (
                            SELECT 
                                los_.product_goods_id, 
                                SUM(los_.berat_loss) AS berat_loss
                            FROM tdProduct_Goods_Loss AS los_
                            WHERE los_.loss_seitai_id = 1 -- ponsu
                            GROUP BY los_.product_goods_id
                        ) ponsu ON good.id = ponsu.product_goods_id
                        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
                        INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id 
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY prd.id;
        
                "));
                    break;
                case '6':
                    return collect(DB::select("
                        SELECT 
                            MAX(dep.name) AS department_name,
                            MAX(prGrp.code) AS product_group_code,
                            MAX(prGrp.name) AS product_group_name,
                            SUM(good.qty_produksi) AS qty_produksi,
                            SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                            SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                            SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                            COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                            SUM(good.infure_berat_loss) AS infure_berat_loss
                        FROM tdProduct_Goods AS good 
                        LEFT JOIN (
                            SELECT 
                                los_.product_goods_id, 
                                SUM(los_.berat_loss) AS berat_loss
                            FROM tdProduct_Goods_Loss AS los_
                            WHERE los_.loss_seitai_id = 1 -- ponsu
                            GROUP BY los_.product_goods_id
                        ) ponsu ON good.id = ponsu.product_goods_id
                        INNER JOIN msMachine AS mac ON good.machine_id = mac.id 
                        INNER JOIN msDepartment AS dep ON mac.department_id = dep.id 
                        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
                        INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id 
                        INNER JOIN msProduct_group AS prGrp ON prT.product_group_id = prGrp.id 
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, prGrp.id;
        
                    "));
                    break;         
                case '7':
                    return collect(DB::select("
                        SELECT 
                            MAX(dep.name) AS department_name,
                            MAX(prT.code) AS product_type_code,
                            MAX(prT.name) AS product_type_name,
                            SUM(good.qty_produksi) AS qty_produksi,
                            SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                            SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                            SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                            COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                            SUM(good.infure_berat_loss) AS infure_berat_loss
                        FROM tdProduct_Goods AS good 
                        LEFT JOIN (
                            SELECT 
                                los_.product_goods_id, 
                                SUM(los_.berat_loss) AS berat_loss
                            FROM tdProduct_Goods_Loss AS los_
                            WHERE los_.loss_seitai_id = 1 -- ponsu
                            GROUP BY los_.product_goods_id
                        ) ponsu ON good.id = ponsu.product_goods_id
                        INNER JOIN msMachine AS mac ON good.machine_id = mac.id 
                        INNER JOIN msDepartment AS dep ON mac.department_id = dep.id 
                        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
                        INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id 
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, prT.id;
        
                    "));  
                        break; 
                case '8':
                    return collect(DB::select("
                        SELECT 
                            MAX(dep.name) AS department_name,
                            MAX(man.employeeNo) AS employeeNo,
                            MAX(man.empName) AS empName,
                            SUM(good.qty_produksi) AS qty_produksi,
                            SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                            SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                            SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                            COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                            SUM(good.infure_berat_loss) AS infure_berat_loss
                        FROM tdProduct_Goods AS good
                        LEFT JOIN (
                            SELECT 
                                los_.product_goods_id, 
                                SUM(los_.berat_loss) AS berat_loss
                            FROM tdProduct_Goods_Loss AS los_
                            WHERE los_.loss_seitai_id = 1 -- ponsu
                            GROUP BY los_.product_goods_id
                        ) ponsu ON good.id = ponsu.product_goods_id
                        INNER JOIN msEmployee AS man ON good.employee_id = man.id  
                        INNER JOIN msDepartment AS dep ON man.department_id = dep.id 
                        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
                        INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id 
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, good.employee_id;
        
                    "));
                        break; 
                case '9':
                    return collect(DB::select("
                            SELECT
                            prd.code || ' ' || prd.name AS product_code,
                            lpk.lpk_no,
                            good.nomor_palet,
                            good.production_date,
                            good.work_shift,
                            good.nomor_lot,
                            good.qty_produksi,
                            (good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                            (good.qty_produksi / prd.case_box_count) AS qty_produksi_box,
                            good.kenpin_qty_loss_proses,
                            (good.kenpin_qty_loss_proses * prd.unit_weight * 0.001) AS kenpin_qty_berat_proses,
                            (good.kenpin_qty_loss_proses / prd.case_box_count) AS kenpin_qty_box_proses,
                            good.kenpin_qty_loss,
                            (good.kenpin_qty_loss * prd.unit_weight * 0.001) AS kenpin_qty_berat,
                            (good.kenpin_qty_loss / prd.case_box_count) AS kenpin_qty_box
                        FROM tdProduct_Goods AS good
                        INNER JOIN tdOrderLpk AS lpk ON good.lpk_id = lpk.id
                        INNER JOIN msProduct AS prd ON good.product_id = prd.id
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar';
                     "));
                        break;    
                case '10':
                    return collect(DB::select("
                        SELECT 
                            max(dep.name) AS department_name,
                            max(mslosCls.name) AS loss_class_name,
                            max(mslos.code) AS loss_code,
                            max(mslos.name) AS loss_name,	
                            SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                            SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan
                        FROM tdProduct_Goods AS good
                        INNER JOIN tdProduct_Goods_Loss AS det ON good.id = det.product_goods_id 
                        INNER JOIN msLossSeitai AS mslos ON det.loss_seitai_id = mslos.id 
                        INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id 
                        INNER JOIN msMachine AS mac ON good.machine_id = mac.id 
                        INNER JOIN msDepartment AS dep ON mac.department_id = dep.id 
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, det.loss_seitai_id;
        
                    "));
                        break;
                case '11':
                    return collect(DB::select("
                        SELECT 
                            max(dep.name) AS department_name,
                            max(prGrp.code || ' : ' || prGrp.name) AS product_group_name,
                            max(mslosCls.name) AS loss_class_name,
                            max(mslos.code) AS loss_code,
                            max(mslos.name) AS loss_name,	
                            SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                            SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan
                        FROM tdProduct_Goods AS good
                        INNER JOIN tdProduct_Goods_Loss AS det ON good.id = det.product_goods_id 
                        INNER JOIN msLossSeitai AS mslos ON det.loss_seitai_id = mslos.id 
                        INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id 
                        INNER JOIN msMachine AS mac ON good.machine_id = mac.id 
                        INNER JOIN msDepartment AS dep ON mac.department_id = dep.id 
                        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
                        INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id 
                        INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id 
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, prGrp.id, det.loss_seitai_id;  
        
                    "));
                         break;
                case '12':
                    return collect(DB::select("
                        SELECT 
                            max(dep.name) AS department_name,
                            max(mac.employeeNo) AS employeeNo,
                            max(mac.empName) AS empName,
                            sum(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                            sum(good.seitai_berat_loss) AS seitai_berat_loss,
                            COALESCE(SUM(loss.berat_loss_01), 0) AS berat_loss_01,
                            COALESCE(SUM(loss.berat_loss_02), 0) AS berat_loss_02,
                            COALESCE(SUM(loss.berat_loss_03), 0) AS berat_loss_03,
                            COALESCE(SUM(loss.berat_loss_04), 0) AS berat_loss_04,
                            COALESCE(SUM(loss.berat_loss_05), 0) AS berat_loss_05,
                            COALESCE(SUM(loss.berat_loss_06), 0) AS berat_loss_06,
                            COALESCE(SUM(loss.berat_loss_07), 0) AS berat_loss_07,
                            COALESCE(SUM(loss.berat_loss_99), 0) AS berat_loss_99
                        FROM tdProduct_Goods AS good
                        LEFT JOIN LATERAL (
                            SELECT
                                SUM(CASE WHEN mslosCls.code = '01' THEN los_.berat_loss ELSE 0 END) AS berat_loss_01,
                                SUM(CASE WHEN mslosCls.code = '02' THEN los_.berat_loss ELSE 0 END) AS berat_loss_02,
                                SUM(CASE WHEN mslosCls.code = '03' THEN los_.berat_loss ELSE 0 END) AS berat_loss_03,
                                SUM(CASE WHEN mslosCls.code = '04' THEN los_.berat_loss ELSE 0 END) AS berat_loss_04,
                                SUM(CASE WHEN mslosCls.code = '05' THEN los_.berat_loss ELSE 0 END) AS berat_loss_05,
                                SUM(CASE WHEN mslosCls.code = '06' THEN los_.berat_loss ELSE 0 END) AS berat_loss_06,
                                SUM(CASE WHEN mslosCls.code = '07' THEN los_.berat_loss ELSE 0 END) AS berat_loss_07,
                                SUM(CASE WHEN mslosCls.code = '99' THEN los_.berat_loss ELSE 0 END) AS berat_loss_99
                            FROM tdProduct_Goods_Loss AS los_
                            INNER JOIN msLossSeitai AS mslos ON los_.loss_seitai_id = mslos.id 
                            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id 
                            WHERE good.id = los_.product_goods_id AND mslos.id <> 1 
                            GROUP BY los_.product_goods_id
                        ) AS loss ON true
                        INNER JOIN msEmployee AS mac ON good.employee_id = mac.id 
                        INNER JOIN msDepartment AS dep ON mac.department_id = dep.id 
                        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY dep.id, good.employee_id;
        
                    "));    
                        break;            
                case '13':
                    return collect(DB::select("
                       SELECT 
                            max(mac.machineNo || ' : ' || mac.machineName) AS machine_name,
                            max(mslosCls.name) AS loss_class_name,
                            max(mslos.code) AS loss_code,
                            max(mslos.name) AS loss_name,    
                            SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                            SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan
                        FROM tdProduct_Goods AS good
                        INNER JOIN tdProduct_Goods_Loss AS det ON good.id = det.product_goods_id
                        INNER JOIN msLossSeitai AS mslos ON det.loss_seitai_id = mslos.id
                        INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
                        INNER JOIN msMachine AS mac ON good.machine_id = mac.id
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
                        GROUP BY mac.id, det.loss_seitai_id;
        
                    "));
                        break;            
                case '14':
                    return collect(DB::select("
                         SELECT 
                            MAX(prGrp.code) AS product_group_code,
                            MAX(prGrp.name) AS product_group_name,
                            MAX(mac.machineNo) AS machine_no,
                            MAX(mac.machineName) AS machine_name,
                            SUM(good.qty_produksi) AS qty_produksi,
                            SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                            MAX(mac.capacity_kg) AS capacity_kg,
                            MAX(mac.capacity_lembar) AS capacity_lembar--,
                            --@day AS seq_no 
                        FROM tdProduct_Goods AS good 
                        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
                        INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id 
                        INNER JOIN msProduct_group AS prGrp ON prT.product_group_id = prGrp.id 
                        INNER JOIN msMachine AS mac ON good.machine_id = mac.id 
                        WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar' 
                        GROUP BY prGrp.name, good.machine_id;   
        
                    "));
                    break;
                    default:
                    
            }
         }  
    }

    public function headings(): array
    {

       
            switch($this->jenisreport) {
                case '1':
                    if ($this->nipon==1){
                        return [
                            'machine_no','machine_name','department_name','berat_standard','berat_produksi','infure_cost',
                            'infure_berat_loss','panjang_produksi','panjang_printing_inline','infure_cost_printing',
                            'work_hour_mm','work_hour_off_mm','work_hour_on_mm'
                        ];
                    }else{
                        return [
                            'No Mesin',	'Nama Mesin','Departeman','Jumlah Produksi','Berat Produksi','Sseitai Cost',	
                            'Berat loss Seitai','Seitai Berat Loss Ponsu','Infure Berat Loss','Jam Mulai Kerja',	
                            'Work Hour Off','Work Hour On'
                        ];
                    }    
                    break;
    
                case '2':
                    if ($this->nipon==1){
                        return[
                            'Departemen','Type Produk','No Mesin','Mesin','Berat Standart','Berat Produksi','Cost produksi',
                            'Berat Loss (Kg)','Panjang Produksi','Panjang Inline Printing','Cost Printing Produksi'
                        ];
                    }else{
                        return[
                            'Departemen','Type Produk','No Mesin','Mesin',	
                            'Jumlah Produksi','Berat Produksi','Seitai Cost',	
                            'Berat loss Seitai','Seitai Berat Loss Ponsu','Infure Berat Loss'
                        ]; 
                    }    
                    break;
    
                    case '3':
                        if ($this->nipon==1){
                            return[
                                'product_group_code','product_group_name','Berat Standart','Berat Produksi',
                                'Cost Produksi','Berat Loss Produksi','Panjang Produksi',
                                'Panjang Inline Printing',	'Cost Printing Produksi'

                            ];
                        }else{
                            return[
                               'product_group_code','product_group_name',	
                               'Jumlah Produksi','Berat Produksi','Seitai Cost',	
                                'Berat loss Seitai','Seitai Berat Loss Ponsu','Infure Berat Loss'
                            ]; 
                        }        
                        break;

                    case '4':
                       if ($this->nipon==1){
                        return[
                                'Kode Produk Tipe','Tipe Produk'	
                                ,'Berat Standart','Berat Produksi',
                                'Cost Produksi','Berat Loss Produksi','Panjang Produksi',
                                'Panjang Inline Printing',	'Cost Printing Produksi'

                            ];
                        }else{
                            return[
                                'Kode Produk Tipe','Tipe Produk',	
                               'Jumlah Produksi','Berat Produksi','Seitai Cost',	
                                'Berat loss Seitai','Seitai Berat Loss Ponsu','Infure Berat Loss'
                            ]; 
                        }  
                        break;   
                    case '5':
                        if ($this->nipon==1){
                            return[
                                'Kode Produk',	'Nama Produk'	
                                ,'Berat Standart','Berat Produksi',
                                'Cost Produksi','Berat Loss Produksi','Panjang Produksi',
                                'Panjang Inline Printing',	'Cost Printing Produksi'
                            ];
                        }else{
                            return[
                               'Kode Produk',	'Nama Produk',
                               'Jumlah Produksi','Berat Produksi','Seitai Cost',	
                                'Berat loss Seitai','Seitai Berat Loss Ponsu','Infure Berat Loss'
                            ]; 
                        }  
                        break;

                    case '6':
                        if ($this->nipon==1){
                            return[
                                'Departemen','Kode Group Produk','Nama Group Produk'	
                                ,'Berat Standart','Berat Produksi',
                                'Cost Produksi','Berat Loss Produksi','Panjang Produksi',
                                'Panjang Inline Printing',	'Cost Printing Produksi'

                            ];
                        }else{
                            return[
                                'Departemen','Kode Group Produk','Nama Group Produk',	
                                'Jumlah Produksi','Berat Produksi','Seitai Cost',	
                                'Berat loss Seitai','Seitai Berat Loss Ponsu','Infure Berat Loss'
                            ]; 
                        }  
                        break;  

                    case '7':
                        if ($this->nipon==1){
                            return[
                                'Departemen','Kode Tipe Produk','Tipe Produk'	
                                ,'Berat Standart','Berat Produksi',
                                'Cost Produksi','Berat Loss Produksi','Panjang Produksi',
                                'Panjang Inline Printing',	'Cost Printing Produksi'
                            ];
                        }else{
                            return[
                                'Departemen','Kode Tipe Produk','Tipe Produk',	
                                'Jumlah Produksi','Berat Produksi','Seitai Cost',	
                                'Berat loss Seitai','Seitai Berat Loss Ponsu','Infure Berat Loss'
    

                            ]; 
                        }    
                            break; 

                    case '8':
                        if ($this->nipon==1){
                            return[
                                'Departemen','Petugas Id','Nama Petugas'	
                                ,'Berat Standart','Berat Produksi',
                                'Cost Produksi','Berat Loss Produksi','Panjang Produksi',
                                'Panjang Inline Printing',	'Cost Printing Produksi'

                            ];
                        }else{
                            return[
                                'Departemen','Petugas Id','Nama Petugas',	
                                'Jumlah Produksi','Berat Produksi','Seitai Cost',	
                                'Berat loss Seitai','Seitai Berat Loss Ponsu','Infure Berat Loss'
                            ]; 
                        }  
                            break; 

                    case '9':
                        if ($this->nipon==2){
                            return[
                                'product_code',	'lpk_no',	'nomor_palet',	'production_date',	'work_shift',	
                                'nomor_lot',	'qty_produksi',	'berat_produksi',	'qty_produksi_box','kenpin_qty_loss_proses',
                                'kenpin_qty_berat_proses',	'kenpin_qty_box_proses','kenpin_qty_loss',	
                                'kenpin_qty_berat',	'kenpin_qty_box'
                            ];
                        } 
                            break;  

                    case '10':
                        if ($this->nipon==1){
                            return[
                                'Departemen','Kelompok Loss','Kode Loss','Loss Name','Berat Loss Produksi',	'Berat Loss Kebutuhan'
                            ];
                        }else{
                            return[
                                 'Departemen','Kelompok Loss','Kode Loss','Loss Name','Berat Loss Produksi','Berat Loss Kebutuhan'
                            ]; 
                        }  
                            break;

                    case '11':
                        if ($this->nipon==1){
                            return[
                                'Departemen','Kelompok Produk',	
                                'Kelompok Loss','Kode Loss','Loss Name','Berat Loss Produksi',	'Berat Loss Kebutuhan'
                            ];
                        }else{
                            return[
                                'Departemen','Kelompok Produk',	
                                'Kelompok Loss','Kode Loss','Loss Name','Berat Loss Produksi',	'Berat Loss Kebutuhan'
                            ]; 
                        }  
                             break;

                    case '12':
                        if ($this->nipon==1){
                            return[
                                'Departemen','Petugas Id',	'Petugas',	'Berat Produksi',
                                'Berat Loss Produksi','Berat Loss 01','Berat Loss 02','Berat Loss 03',
                                'Berat Loss 04','Berat Loss 05','Berat Loss 06','Berat Loss 07','Berat Loss 99',
                                'Berat Loss Seitai Infure'

                            ];
                        }else{
                            return[
                                'Departemen','Petugas Id',	'Petugas',	'Berat Produksi',
                                'Berat Loss Produksi','Berat Loss 01','Berat Loss 02','Berat Loss 03',
                                'Berat Loss 04','Berat Loss 05','Berat Loss 06','Berat Loss 07','Berat Loss 99'
                            ]; 
                        }  
                            break;  

                    case '13':
                        if ($this->nipon==1){
                            return[
                                'Nama Mesin','Kelompok Nama Loss','Kode Los','Nama Loss',
                                'Berat Loss Produksi','Berat Loss Kebutuhan'
                            ];
                        }else{
                            return[
                               'Nama Mesin','Kelompok Nama Loss','Kode Los','Nama Loss',
                                'Berat Loss Produksi','Berat Loss Kebutuhan'
                            ]; 
                        }  
                            break;  

                    case '14':
                        if ($this->nipon==1){
                            return[
                                'Kode Group Produk','Nama Group Produk','No Mesin','Nama Mesin','Panjang Produksi',
                                'Berat Produksi','Kapacity (Kg)','Kapacity(lembar)'

                            ];
                        }else{
                            return[
                                'Kode Group Produk','Nama Group Produk','No Mesin','Nama Mesin',	
                                'Jumlah Produksi','Berat Produksi','Kapacity (Kg)','Kapacity(lembar)'
                            ]; 
                        }  
                        break;
                    default:
                    
            }
         
        
    }
}

