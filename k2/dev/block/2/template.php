<ul class="map"><?
for($i=0; $i<count($this->List); $i++)
{
	if($this->List[$i]['LEVEL'] < $this->List[$i - 1]['LEVEL']){
		?></ul><?
	}
	if($this->List[$i - 1]['LEVEL'] && $this->List[$i]['LEVEL'] > $this->List[$i - 1]['LEVEL']){
		?><ul><?
	}
	?><li><a href="<?=$this->List[$i]['URL']?>"><?=$this->List[$i]['NAME']?></a></li><?
}
?></ul>