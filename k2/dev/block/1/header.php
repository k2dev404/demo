<?php if($_SERVER['REQUEST_URI']=='/index.php') { header("HTTP/1.1 301 Moved Permanently"); header("Location: /"); exit(); } ?>
<div class="slider <?
	if($CURRENT['SECTION']['ID'] == 1){
		?>index<?
	}else{
		?>inside<?
	}
	?>">
	<div class="head <?
	if($CURRENT['SECTION']['ID'] == 1){
		?>index<?
	}else{
		?>inside<?
	}
	?>">
    	<div class="box">
        	<a href="/" class="logo"></a>
            <a href="<?=$URL[29]?>" class="calc"><span class="op">Калькулятор<br>стоимости</span></a>
            <a href="<?=$URL[27]?>" class="win"><span class="op">Подбор<br>окна</span></a>
            <a href="" class="MORun"><span class="op">Мобильный<br>офис</span></a>
            
        	<div id="overlay" style="display:none;">
        		<div class="place">
        			<a class="close" href=""><img src="/i/mo/close.png"></a>
        			<iframe src="/mobilnij-ofis" width="400" height="600"></iframe>
        		</div>	
        	</div>	
            
            <div class="contact">
            	<?city()?>
            	<div class="phone"><?=$CITY[-1]['PHONE']?></div>
                <a href="#" data-url="<?=$URL[28]?>?ajax=1" class="button op modal">Вызвать замерщика</a>
            </div>
        </div>
    </div>
    <div class="menu<?
    if($CURRENT['SECTION']['ID'] == 1){
		?> index<?
	}
    ?>">
    	<div class="box"><?$LIB['NAV']->Menu(3, array('ACTIVE' => 1))?></div>
	</div>
	<div class="box" style="position:relative">
    	<?spec()?>
    </div>
</div>