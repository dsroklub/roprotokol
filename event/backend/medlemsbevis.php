<?php
$_SERVER['DOCUMENT_ROOT']="/data/roprotokol/xx";
#include("../../rowing/backend/inc/common.php");
include("/data/roprotokol/rowing/backend/inc/common.php");
include("utils.php");

if (isset($_GET["member"])) {
    $member=$_GET["member"];
} else {
    echo "please set member";
    exit(0);
}

require_once('/usr/share/php/tcpdf/tcpdf.php');

$qm="SELECT CONCAT(FirstName,' ',LastName) as name, MemberID as member_id FROM Member WHERE MemberID=?";
$qr="SELECT MemberRight as r,argument,DATE_FORMAT(acquired,'%e/%c %Y') as acquired,showname FROM Member,MemberRights,MemberRightType
  WHERE MemberID=? AND Member.id=MemberRights.member_id
AND MemberRightType.member_right=MemberRights.MemberRight AND NOT (MemberRightType.arg <> MemberRights.argument)
AND MemberRightType.active>0 AND MemberRightType.category='roning'
ORDER BY MemberRights.acquired";
$rstmt = $rodb->prepare($qr) or die("bevis right prep ". mysqli_error($rodb));
$rstmt->bind_param("s", $member);
$rstmt->execute() or die("bevis right exe");
$rres=$rstmt->get_result();
$mstmt = $rodb->prepare($qm) or die("bevis prep");
$mstmt->bind_param("s", $member);
$mstmt->execute() or die("bevis exe");
$mres=$mstmt->get_result();
$rower = $mres->fetch_assoc() or die("$member findes ikke");
$rowername=$rower["name"];
$member_id=$rower["member_id"];

$cusername=null;
if (isset($_SERVER['PHP_AUTH_USER']) && !isset($_GET["nonamesign"])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
    $stmt=$rodb->prepare("SELECT CONCAT(FirstName,' ',LastName) as name FROM Member WHERE Member.MemberId=?") or dbErr($rodb,$res," mbevis cuser");
    $stmt->bind_param("s", $cuser) or dbErr($rodb,$res," mb bind");
    $stmt->execute() or dbErr($rodb,$res,"mb user exe");
    $cres= $stmt->get_result() or dbErr($rodb,$res,"mb user res");
    if ($cres->num_rows==1) {
        $cusername=$cres->fetch_assoc()['name'];
    }
}


$pdf = new TCPDF('P', 'mm','A4', true, 'UTF-8', false,true);
$pdf->SetPrintHeader(false);
//$pdf->setPrintFooter(false);
$pdf->SetCreator("DSR roprotokol");
//$pdf->SetAuthor('');
$pdf->SetTitle('Medlemsrettigheder for $rowername');
$pdf->SetSubject('Rettigheder for DSR medlem');


$pdf->AddPage();
//$mres->close();


$pdf->SetAuthor("Danske Studenters Roklub",true);
$pdf->SetCreator("roprotokollen",true);
$pdf->SetTitle("Rettigheder for DSR medlem",true);

$pdf->SetFont('Times','B',16);

$pdf->ImageSVG($file='/data/media/DSR_logo_text.svg', $x=4, $y=4, $w='', $h=35, $link='', $align='', $palign='', $border=0, $fitonpage=true);

$pdf->Ln(40);
$pdf->Cell($w=20,0,"Rettigheder for medlem $rowername:",$border=0, $ln=1,$align='L',$fill=null);
$pdf->Ln(13);
$pdf->SetFont('Helvetica','B',12);

while ($right = $rres->fetch_assoc()) {
    $rarg=$right["argument"];
    if ($rarg=="row") {
        $rarg="robåde";
    }
    $pdf->Cell($w=0,8,$right["showname"]." $rarg ".$right["acquired"],$border=0, $ln=1,$align='L',$fill=null);
}

$pdf->Ln(10);
$today=date('d/m Y',time());
$pdf->Cell($w=200,0,"For DSR den $today: ____________________",$border=0, $ln=1,$align='C',$fill=null);
if ($cusername) {
    $pdf->Ln(5);
    $pdf->Cell($w=200,0,"                 $cusername",$border=0, $ln=1,$align='C',$fill=null);
}

header("Content-type: application/pdf");
header("Content-Disposition: attachment;filename=roer${member_id}.pdf");
$pdf->Output("roerrettigheder__$member_id.pdf",'I');
