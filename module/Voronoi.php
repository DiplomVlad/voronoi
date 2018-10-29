<?php
/**
 * Created by PhpStorm.
 * User: Deik
 * Date: 31.07.2018
 * Time: 0:21
 */
    require_once 'PointChunk.php';
    require_once 'Edge.php';

    class Voronoi
    {
        private $metrics = 1; //1-Еклидова, 2-Манхеттенская, 3-Минковского
        private $points = [];
        private $bbox = null;

        public function __construct($metrics, $size) {
            $this->metrics = $metrics;
            $this->bbox = [
                'borders' => $size['borders'],
                'minwidth' => $size['minwidth'],
                'minheight' => $size['minheight'],
                'width' => $size['width'],
                'height' => $size['height']-2
            ];
            foreach ($size['sites'] as $value){
                $this->points[$value->getId()] = $value;
            }

            for ($j=0; $j<count($this->bbox['borders']); $j++) {
                $newbor = new Edge($this->bbox['borders'][$j], (($j + 1) != count($this->bbox['borders'])) ? $this->bbox['borders'][$j + 1] : $this->bbox['borders'][0]);
                foreach ($this->points as $key => $value)
                    $this->points[$key]->edges[$newbor->getId()] = $newbor;
            }
        }
		
		private function updateEdges($point,$value,$perpendicular,$midpoint){
            echo "<pre>";
                //if($point->getId()=="150-0")print_r($point->edges);
            echo "</pre>";
			$line = new Edge($point, $value);
            $A = new Point(999999,999999,null,false);
			$keyLA = '';
            $B = new Point(999999,999999,null,false);
			$keyLB = '';
			echo '-------updateEdges with '.$value->getId().'--------<br/>';
            foreach ($point->edges as $k => $val) {
                $tmp = Edge::overlaps($val, $perpendicular);
                /*if($midpoint->getId()=="150-150"){

                    echo  "<pre>";
                    echo $val->coefficient."<br>";
                    echo $val->shift."<br>";
                    echo $perpendicular->coefficient."<br>";
                    echo $perpendicular->shift."<br>";
                    echo "</pre>";
                }*/
                //if ($tmp != null)echo "tmp!=null--".$tmp->getId()."<br>";
                if ($tmp != null && $tmp->IsPointInsidePolygon($this->bbox['borders'])) {
                    if($line->getCoefficient()===null){
                        $localResult = $line->shift;
                        if ((PointChunk::length($A, $midpoint) > PointChunk::length($tmp, $midpoint)) &&
                            $localResult<=$tmp->x && $B->getId()!=$tmp->getId()){
                            $A = $tmp;
                            $keyLA = $k;
                        }
                        if ((PointChunk::length($B, $midpoint) > PointChunk::length($tmp, $midpoint)) &&
                            $localResult>=$tmp->x && $tmp->getId()!=$A->getId()){
                            $B = $tmp;
                            $keyLB = $k;
                        }
                    }else{
                        $localResult = $line->getCoefficient()*$tmp->x+$line->shift;
                        if ((PointChunk::length($A, $midpoint) > PointChunk::length($tmp, $midpoint)) &&
                            $localResult<=$tmp->y && $B->getId()!=$tmp->getId()){
                            if($localResult==$tmp->y&&$B->getId()!="999999-999999"||$localResult<$tmp->y){
                                $A = $tmp;
                                $keyLA = $k;
                            }
                        }
                        if ((PointChunk::length($B, $midpoint) > PointChunk::length($tmp, $midpoint)) &&
                            $localResult>=$tmp->y && $tmp->getId()!=$A->getId()){
                            //if($localResult==$tmp->y&&$A->getId()!="999999-999999"||$localResult>$tmp->y) {
                                $B = $tmp;
                                $keyLB = $k;
                            //}
                        }
                    }
					if($keyLB == '' || $keyLA == '300-300|200-0'||$point->getId()=="150-0") {
                        //echo (($line->getCoefficient()===null)?"1":"0")."<br>";
                        //echo $perpendicular->coefficient."<br>";
                        //echo $perpendicular->shift."<br>";
						echo "val->getId() = ".$val->getId()."; Overlaps = ".$tmp->getId()."; keyLB:".$keyLB."; keyLA:".$keyLA."; A:".$A->getId()."; B:".$B->getId()."<br>";
					}
                }
            }
            if($keyLB == '' || $keyLA == '') {
				//echo "keyLB:".$keyLB."; keyLA:".$keyLA."; A:".$A->getId()."; B:".$B->getId()."<br>";
			}else{
                $idEdges=Edge::newEdge($point, $value, [$A,$B], [$keyLA,$keyLB]);
                $value->edges[$A->getId()."|".$B->getId()]=$point->edges[$A->getId()."|".$B->getId()] = new Edge($A, $B);
                if($value->status == false){
                    unset($value->edges[$keyLA]);
                    unset($value->edges[$keyLB]);
                }
                unset($point->edges[$keyLA]);
                unset($point->edges[$keyLB]);
            }
            //foreach ($idEdges as $key=>$item){
                //echo $key.":{".$item."}<br>";
            //}
            //echo "AB:{".$A->getId()."|".$B->getId()."}<br>";
           // echo "---------------------------------------------------------<br><br>";
            //echo $point->getId()."|".$value->getId();
            /*if(($point->getId()."|".$value->getId())=="150-0|150-300") {
                echo $point->edges[$A->getId()."|".$B->getId()]->getId()."pro<br>".$midpoint->getId()."<br>";
            }
            if(strnatcmp($point->getId(),"150-0")!=0) {
                    print_r($point->edges);
            }*/
            ksort($point->edges);
            //ksort($value->edges);
        }

        public function edges($point){
            echo "Point P:".$point->getId()."<p>";
            foreach($this->points as $value){
                if(strnatcmp($value->getId(),$point->getId())!=0){
                    //echo "Point V:".$value->getId()."<br>";
                    $perpendicular = new Edge($point, $value);
                    $midpoint = PointChunk::midpoint($point,$value);

                    $perpendicular->perpendicular($midpoint);
                    /*if($point->getId()=="150-0"){

                        //echo  "<pre>";
                        echo $perpendicular->coefficient."<br>";
                        echo $perpendicular->shift."<br>";
                        //print_r($tmp);
                        echo "--------------------------------------<br>";
                    }*/
                    /*echo "<pre>";
                        print_r($point);
                    echo "</pre>";*/
                    $this->updateEdges($point,$value,$perpendicular,$midpoint);
                    /*echo $point->getId()."|".$value->getId();
                    if(($point->getId()."|".$value->getId())=="150-0|450-0") {
                        echo "<pre>";
                        print_r($line);
                        echo "</pre>";
                        echo($value->getId() . ":" . $point->getId() . ":" . strnatcmp($value->getId(), $point->getId()));
                    }*/
                    //$this->updateEdges($value,$line,$midpoint);
                    if($value->status == false)$value->closeEdge();
                }
            }
            echo "</p><pre>";
                //print_r($point->edges);
            echo "</pre>";
            $point->closeEdge();
            $point->status = true;
        }

        public function Diagram() { //Диаграмма
            ksort($this->points);
            /*echo "<pre>";
            print_r($this->points);
            echo "</pre>";*/
            foreach ($this->points as $point) {
                $this->edges($point);
            }
            foreach ($this->points as $point) {
                echo "<p>id: ".$point->getId()."<br>x:".$point->x."<br>y:".$point->y."<br>edges: <br>";
                foreach ($point->edges as $edge){
                    $ab = $edge->getAB();
                    echo "-  {A: x:".$ab[0]->x." y:".$ab[0]->y.", B: x:".$ab[1]->x." y:".$ab[1]->y."}<br>";
                }
                echo "</p>";
            }
        }
    }