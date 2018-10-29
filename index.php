<?php
/**
 * Created by PhpStorm.
 * User: Deik
 * Date: 23.10.2018
 * Time: 21:18
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'module/Voronoi.php';
require_once 'module/PointChunk.php';
?>
<!DOCTYPE html>
<html lang="en">
	<title>
	Map
	</title>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?//php echo $title; ?></title>
	</head>
	<body>
<?php
// Create the border box object

$bbox = [
    'minwidth' => 0,
    'minheight' => 0,
    'width' => 602,
    'height' => 302,
    'borders' => [],
    'countPoint' => 0,
    'sites' => []
];
$bbox['borders'] = [new PointChunk(($bbox['width']-2)/4,0,null,false), new PointChunk(($bbox['width']-2)-(($bbox['width']-2)/4),0,null,false),new PointChunk($bbox['width']-2,($bbox['height']-2)/2,null,false),new PointChunk(($bbox['width']-2)-(($bbox['width']-2)/4),$bbox['height']-2,null,false),new PointChunk(($bbox['width']-2)/4,$bbox['height']-2,null,false),new PointChunk(0,($bbox['height']-2)/2,null,false)];
$bbox['sites'] = $bbox['borders'];
// Create the image
$im = imagecreatetruecolor($bbox['width'], $bbox['height']);
$white = imagecolorallocate($im, 255, 255, 255);
$red = imagecolorallocate($im, 255, 0, 0);
$green = imagecolorallocate($im, 0, 100, 0);
$black = imagecolorallocate($im, 0, 0, 0);
imagefill($im, 0, 0, $white);

// Create random points and draw them
for ($i=0; $i < $bbox['countPoint']; ) {
	$point = new PointChunk($bbox['width'], $bbox['height'], null, true);
    if($point->IsPointInsidePolygon($bbox['borders'])){
        $bbox['sites'][] = $point;$i++;
        imagerectangle($im, $point->x - 2, $point->y - 2, $point->x + 2, $point->y + 2, $black);
        echo $point->x.':'.$point->y.'-'.$i.';<br>';
    }
}

$voronoi = new Voronoi(1,$bbox);
$diagram = $voronoi->Diagram();
?>
<?php
/*$j = 0;
foreach ($diagram['cells'] as $cell) {
	$points = array();

	if (count($cell->_halfedges) > 0) {
		$v = $cell->_halfedges[0]->getStartPoint();
		if ($v) {
			$points[] = $v->x;
			$points[] = $v->y;
		} else {
			var_dump($j.': no start point');
		}

		for ($i = 0; $i < count($cell->_halfedges); $i++) {
			$halfedge = $cell->_halfedges[$i];
			$edge = $halfedge->edge;

			if ($edge->va && $edge->vb) {
				imageline($im, $edge->va->x, $edge->va->y, $edge->vb->x, $edge->vb->y, $red);
			}

			$v = $halfedge->getEndPoint();
			if ($v) {
				$points[] = $v->x;
				$points[] = $v->y;
			} else {
				var_dump($j.': no end point #'.$i);
			}
		}
	}

	// Draw Thyssen polygon
	$color = imagecolorallocatealpha($im, rand(0, 255), rand(0, 255), rand(0, 255), 50);
	imagefilledpolygon($im, $points, count($points) / 2, $color);
	$j++;
}
// Display image
imagepng($im, 'voronoi.png');*/
$tmp = new PointChunk(999999,999999,null,false);
$arr[0]= $tmp;
$np[0]=$tmp;
echo $arr[0]->getId().":".$np[0]->getId()."<br>";
unset($np[0]);
echo $arr[0]->getId();
?>

	</body>
</html>