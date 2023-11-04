<?php
/**
 * raw to sharpen
 * authored by 9r3i
 * https://github.com/9r3i/mhfu-sharpen-texture
 * started at october 22nd 2023
 * required: php and gd image
 **/
$start=microtime(true);
$date=date('Y-m-d H:i:s');
$raw='raw/';
$serial='ULUS10391';
$out='textures';
if(!is_dir($serial.'/'.$out)){
  @mkdir($serial.'/'.$out,0755,true);
}
$ini=$serial.'/textures.ini';
$hi=fopen($ini,'wb');
$base=gzinflate(base64_decode('sywyzgQA'));
fwrite($hi,"; auto-generated on $date\n");
fwrite($hi,"; from https://github.com/9r3i/mhfu-sharpen-texture\n");
fwrite($hi,"; serial: $serial base on $base mhfu code trap\n");
fwrite($hi,"[options]\nversion=1\nhash=quick\n[hashranges]\n[hashes]\n");
$scan=@scandir($raw);
$files=is_array($scan)?array_diff($scan,['.','..']):[];
$total=count($files);
$count=0;
$errSharpen=0;
$errWrite=0;
echo "total: $total files\n";
foreach($files as $file){
  $count++;
  $padded=sprintf('%04d',$count);
  $ofile=$out.'/img'.$padded.'.sharpened.png';
  echo "$padded of $total - $file - ";
  $res=sharpen($raw.$file,$serial.'/'.$ofile);
  echo $res?'OK':'FAILED';
  $write=fwrite($hi,"{$file}={$ofile}\n");
  echo $write?' - OK':' - FAILED';
  echo "\n";
  if(!$res){$errSharpen++;}
  if(!$write){$errWrite++;}
}
fclose($hi);
$time=number_format(microtime(true)-$start,1);
echo "total: $total files\n";
echo "time: $time seconds\n";
echo "error sharpen: $errSharpen\n";
echo "error write: $errWrite\n";

function sharpen(string $png,string $out){
  $image=imagecreatefrompng($png);
  imagesavealpha($image,true);
  $matrix=[[0.0,-1.0,0.0],[-1.0,5.0,-1.0],[0.0,-1.0,0.0]];
  $divisor=array_sum(array_map('array_sum',$matrix));
  imageconvolution($image,$matrix,$divisor,0);
  return imagepng($image,$out);
}

