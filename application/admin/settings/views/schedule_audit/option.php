<?php 
option();
foreach($audit_section[0] as $m0) {
	if($m0->is_active !=0) {
		option($m0->id,$m0->section_code .' - '.$m0->section_name);	
		foreach($audit_section[$m0->id] as $m1) {
			if($m1->is_active !=0) {
				option($m1->id,'&nbsp; |-----'.$m1->section_code .' - '.$m1->section_name);
				foreach($audit_section[$m1->id] as $m2) {
					if($m2->is_active !=0) {
						option($m2->id,'&nbsp; &nbsp; &nbsp; |-----'.$m2->section_code .' - '.$m2->section_name);
                        foreach($audit_section[$m2->id] as $m3) {
                            if($m3->is_active != 0) {
                                option($m3->id,'&nbsp; &nbsp; &nbsp; &nbsp; |-----'.$m3->section_code .' aa- '.$m3->section_name);
                            }
                        }
                    }	
				}
			}	
		}
	} 
}
?>