<?php
/**
 * Created by PhpStorm.
 * User: Deik
 * Date: 31.07.2018
 * Time: 1:25
 */
require_once("PointChunk.php");
require_once("Point.php");

class Edge
{
    public $A = null;
    public $B = null;
    public $length = 0;
    public $coefficient = 0;
    public $shift = 0;
    private $id = "";

    public function __construct($A, $B) {
        $this->A = new Point($A->x,$A->y,null,false);
        $this->B = new Point($B->x,$B->y,null,false);
        $this->id = $A->x."-".$A->y."|".$B->x."-".$B->y;
        //$this->A->edges = null;
        //$this->B->edges = null;
        $this->length = PointChunk::length($A,$B);
        $this->coefficient = (($B->x-$A->x)==0)?null:($B->y-$A->y)/($B->x-$A->x);
        $this->shift = ($this->coefficient==0)?($this->coefficient===null)?$A->x:$A->y:($B->x*$A->y-$A->x*$B->y)/($B->x-$A->x);
    }
    public function getLength(){
        return $this->length;
    }
    public function getCoefficient(){
        return $this->coefficient;
    }
    public function getA(){
        return $this->A;
    }
    public function getB(){
        return $this->B;
    }
    public function getAB(){
        return [$this->A,$this->B];
    }
    public function getId(){
        return $this->id;
    }
    public static function newEdge($point, $value, $AB,$numbers){
        $arr=[];
        for($i=0;$i<2;$i++){
            //echo "-".$numbers[$i]."<br>";
			//$lA = 0;
			//$lB = 0;
			if( isset($point->edges[$numbers[$i]]) ){
				$lA = PointChunk::length($point, $point->edges[$numbers[$i]]->A);
				$lB = PointChunk::length($point, $point->edges[$numbers[$i]]->B);
				//echo "-".$lA.":".$lB."<br>";.$point->getId().":".$AB[$i]->getId().", number:".$numbers[$i];
				$edgeP = ($lA > $lB)?new Edge($AB[$i],$point->edges[$numbers[$i]]->B):new Edge($AB[$i],$point->edges[$numbers[$i]]->A);
				$edgeV =(!empty($value->edges[$numbers[$i]]))?
					 ($lA < $lB)?new Edge($AB[$i],$value->edges[$numbers[$i]]->B):new Edge($AB[$i],$value->edges[$numbers[$i]]->A):
					 ($lA < $lB)?new Edge($AB[$i],$point->edges[$numbers[$i]]->B):new Edge($AB[$i],$point->edges[$numbers[$i]]->A);
				$point->edges[$edgeP->getId()] = $edgeP;$arr[]=$edgeP->getId();
				$value->edges[$edgeV->getId()] = $edgeV;$arr[]=$edgeV->getId();
			}else echo "point->edges[{$numbers[$i]}] was not found!<br/>";
        }
        /*if(($point->getId()."|".$value->getId())=="150-0|150-300") {
            echo "<pre>";
            print_r($arr);
            echo "</pre>";
        }*/
        return $arr;
    }
    public function checkParaler($edge){
        $thisVector = [$this->B->x - $this->A->x, $this->B->y - $this->A->y];
        $ab = $edge->getAB();
        $edgeVector = [$ab[1]->x - $ab[0]->x, $ab[1]->y - $ab[0]->y];
        /*echo $this->getId();
        echo "<pre>";
        print_r($thisVector);
        echo $edge->getId();
        print_r($edgeVector);
        echo "</pre>";
        echo "<br>".(($thisVector[1]==0)?0:($thisVector[0]/$thisVector[1]))."=".(($edgeVector[1]==0)?0:($edgeVector[0]/$edgeVector[1]))."<br>";*/
        return ((($thisVector[1]==0)?0:($thisVector[0]/$thisVector[1]))!=(($edgeVector[1]==0)?0:($edgeVector[0]/$edgeVector[1])))?true:false;
    }
    public function setPoint($point, $newEdge)
    {
        //$len = Point::length($point,$newEdge);
        $lA = PointChunk::length($point, $this->A);
        $lB = PointChunk::length($point, $this->B);
        if ($lA > $lB){
            //if($lA>$len){
            $this->A = $newEdge;
            //}
        }else{
            //if($lB>$len){
            $this->B = $newEdge;
           // }
        }
        $this->length = PointChunk::length($this->A, $this->B);
        $this->coefficient = (($this->B->x - $this->A->x) === 0) ? null : ($this->B->y - $this->A->y) / ($this->B->x - $this->A->x);
        $this->shift = ($this->coefficient==0)?($this->coefficient === null) ? $this->A->x:$this->A->y:($this->B->x*$this->A->y-$this->A->x*$this->B->y)/($this->B->x-$this->A->x);
    }

    public function perpendicular($midpoint){
        if($this->coefficient===null){
            $this->coefficient = 0;
            $this->shift = $midpoint->y;
        }else{
            $this->coefficient = ($this->coefficient==0)? null : - 1/$this->coefficient;
            $this->shift = ($this->coefficient===null)? $midpoint->x : $midpoint->y - $this->coefficient*$midpoint->x;
        }
        //echo "A:".$this->A->y."B:".$this->B->y.$this->coefficient."<br>";
    }

    public function sort($points){
        uasort($points,'cmp');
        return $points;
    }

    private function cmp($A, $B){ return ($A->x>=$B->x)?1:-1; }

    public function coefficient($A, $B) {
        return ($B->y-$A->y)/($B->x-$A->x);
    }

    public function lengthMidpoint($point) {
        return PointChunk::length(PointChunk::midpoint($this->A,$this->B),$point);
    }
    public function lengthPoint($point) {
        //{Реализация алгоритма и вывод результатов}
        if($point==null) return null;
        $r1=PointChunk::length($point,$this->B);
		$r2=PointChunk::length($point,$this->A);
		$r12=PointChunk::length($this->B,$this->A);
		if($r1>=PointChunk::length(new Point($r2,$r12,null,false),new Point(0,0,null,false))){
            return $r2;
        }else{
            if($r2>=PointChunk::length(new Point($r1,$r12,null,false),new Point(0,0,null,false))){
                return $r1;
            }else{
                $y1=$this->A->y-$this->B->y; $x1=$this->B->x-$this->A->x; $xy1=-$this->B->x*($this->A->y-$this->B->y)+$this->B->y*($this->A->x-$this->B->x);	$l=PointChunk::length (new Point($y1,$x1,null, false), new Point(0,0, null, false));
				if($xy1>0){
                    $y1=-$y1; $x1=-$x1; $xy1=-$xy1;
                }
				$r0=($y1*$point->x+$x1*$point->y+$xy1)/$l;
				if($r0<0) return ($r0)*-1;
				return $r0;
			}
        }
	}

    public function shift($A, $B) {
        return -(($B->y-$A->y)/($B->x-$A->x)) * $A->x + $A->y;
    }

    public static function overlaps($edge1,$edge2){
        if($edge1->coefficient!=$edge2->coefficient) {
            if ($edge2->getCoefficient() === null){
                $newPoint = new Point($edge2->shift, $edge1->coefficient * $edge2->shift + $edge1->shift, null, false);
                /*if ($edge1->getId() == '0-150|150-0') {
                    echo $edge1->coefficient . "<br>";
                    echo $edge1->shift . "<br>";
                    echo "val->getId() = " . $edge1->getId() . "; x = " . $edge2->shift . "; y = " . ($edge1->coefficient * $edge2->shift + $edge1->shift) . "<br>";
                }*/
            }else{
                if($edge2)
                $newPoint =new Point(($edge1->shift-$edge2->shift)/($edge2->coefficient-$edge1->coefficient),($edge1->coefficient*(($edge1->shift-$edge2->shift)/($edge2->coefficient-$edge1->coefficient))+$edge1->shift),null, false);
            }
            return ($edge1->lengthPoint($newPoint)==0)?$newPoint:null;
        }else return null;
    }
}