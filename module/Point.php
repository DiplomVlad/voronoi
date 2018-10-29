<?php
/**
 * Created by PhpStorm.
 * User: Deik
 * Date: 21.09.2018
 * Time: 0:00
 */

class Point
{
    public $x = 0;
    public $y = 0;
    private $z = 0;
    private $id = "";
    public function __construct($width, $height, $length, $rand){
        if($rand){
            $angle = ((rand(0,999999)/1000000)*pi()*2);
            $radius = [(((rand(0,999999)/1000000)* ($width*0.4 - 0)) + 0), (((rand(0,999999)/1000000)* ($height * 0.5 - 0)) + 0)];
            $this->x = round($radius[0] * cos($angle)) + $width/2;
            $this->y = round($radius[1] * sin($angle)) + $height/2;
            if($length != null){
                $radius = [$width-($width/1.7), $height-($height/2), $length-($length/2)];
                $this->z = round(rand(0, $length) + $radius[3] *sin($angle))+ $length/2;
                $this->id = $this->x."-".$this->y."-".$this->z;
            }else{
                $this->z = $length;
                $this->id = $this->x."-".$this->y;
            }
        }else{
            // On stocke les valeurs
            $this->x = $width;
            $this->y = $height;
            $this->z = $length;
            if($length != null){
                $this->id = $this->x."-".$this->y."-".$this->z;
            }else{
                $this->id = $this->x."-".$this->y;
            }
        }
    }
    public function IsPointInsidePolygon ($bords)
    {
        //i1, i2, n, N, S, S1, S2, S3, flag;
        //echo "---------------------------------------<br>";
        $flag = 0;
        $N = count($bords);
        for ($n=0; $n<$N; $n++)
        {
            $i1 = $n < $N-1 ? $n + 1 : 0;
            while ($flag == 0)
            {
                $i2 = $i1 + 1;
                if ($i2 >= $N)
                    $i2 = 0;
                if ($i2 == ($n < $N-1 ? $n + 1 : 0))
                    break;
                $S = abs ($bords[$i1]->x * ($bords[$i2]->y - $bords[$n ]->y) + $bords[$i2]->x * ($bords[$n ]->y - $bords[$i1]->y) + $bords[$n]->x  * ($bords[$i1]->y - $bords[$i2]->y));
                $S1 = abs ($bords[$i1]->x * ($bords[$i2]->y - $this->y) + $bords[$i2]->x * ($this->y - $bords[$i1]->y) + $this->x * ($bords[$i1]->y - $bords[$i2]->y));
                $S2 = abs ($bords[$n ]->x * ($bords[$i2]->y - $this->y) + $bords[$i2]->x * ($this->y - $bords[$n ]->y) + $this->x * ($bords[$n ]->y - $bords[$i2]->y));
                $S3 = abs ($bords[$i1]->x * ($bords[$n ]->y - $this->y) + $bords[$n]->x * ($this->y - $bords[$i1]->y) + $this->x * ($bords[$i1]->y - $bords[$n ]->y));
                //if(strnatcmp($this->id,"300_0")!=0){echo "sum:".$S." = ".($S1 + $S2 + $S3)."/S:".$S." - S1:".$S1." - S2:".$S2." - S3:".$S3."<br>";
                //print_r($this);}
                //if(strnatcmp($this->id,"75_225")!=0){echo "sum:".$S." = ".($S1 + $S2 + $S3)."/S:".$S." + S1:".$S1." + S2:".$S2." + S3:".$S3."<br>";
                //echo $this->x.$this->y;
                if ($S == $S1 + $S2 + $S3)
                {
                    $flag = 1;
                    //echo "x:".$this->x."- y:".$this->y;
                    //echo "flag:".$flag."<br>";
                    return $flag;
                }
                $i1 = $i1 + 1;
                if ($i1 >= $N)
                    $i1 = 0;
            }
            if ($flag == 0)
                break;
        }
        return $flag;
    }
    public function getId(){
        return $this->id;
    }
    public function checkPoint($point){
        return (($this->x==$point->x)&&($this->y==$point->y))?true:false;
    }
}