
	<div class="userCart">
		<div class="left">
			<form method="post">
			<div class="basket">
				<?
				for ($i = 0, $c = count($this->Cart); $i < $c; $i++) {
					$arItem = $this->Cart[$i];

					$nTotal = $arItem['QUANTITY'] * (int)$arItem['PRICE'];
					?>
					<div class="item">
						<div class="itemLeft">
							<a class="photo" href="<?=$arItem['DATA_TMP']['URL']?>"><img alt="" src="<?=$arItem['DATA_TMP']['PHOTO']?>"></a>
							<a class="name" href="<?=$arItem['DATA_TMP']['URL']?>"><?=$arItem['NAME']?></a>
							<a class="delete" href="?action=delete&id=<?=$arItem['ID']?>">Удалить</a>
						</div>
						<div class="itemRight">
							<div class="price"><?=(int)$nTotal?> руб</div>
							<div class="quantity">
								<div class="n up active"><i></i></div>
								<div class="n down <?if($arItem['QUANTITY'] > 1){?> active<?}?>"><i></i></div>
								<input type="text" name="QUANTITY[<?=$arItem['CODE']?>]" value="<?=$arItem['QUANTITY']?>" readonly>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<?
					$nTotalAll += $nTotal;
				}
				?>
				<div class="stat">
					<dl>
						<dt>Стоимость товаров:</dt>
						<dd><?=$nTotalAll?> руб</dd>
					</dl>
					<dl style="display: none">
						<dt>Доставка:</dt>
						<dd>0 руб</dd>
					</dl>
					<dl>
						<dt>Итого:</dt>
						<dd><b><?=$nTotalAll?> руб</b></dd>
					</dl>
				</div>
			</div>
			</form>
		</div>
		<div class="right">
			<div class="order">
				<div class="menu">
					<?
					foreach($this->Menu as $sAction => $sName)
					{
						?>
						<a href="/cart/?action=<?=$sAction?>"<?
						if($_GET['action'] == $sAction || !$_GET['action'] && $sAction == 'contact'){
							?> class="active"<?
							$bActive = true;
						}
						if($bActive){
							?> onclick="return false;" <?
						}
						?>><?=$sName?></a>
						<?
					}
					?>
					<div class="clear"></div>
				</div>
				<div class="orderBox">
					<?
					if($_GET['action'] == 'delivery'){
						?>
						<div class="item">
							<form action="?action=delivery" method="post">
								<div class="title">Варианты доставки</div>
								<div class="form2">
									<?
									foreach($this->Delivery as $arDelivery)
									{
										?><label><input type="radio" name="DELIVERY" value="<?=$arDelivery['ID']?>"<?
										if($_SESSION['ORDER']['DELIVERY'] == $arDelivery['ID']){
											?> checked<?
										}
										?>><?=$arDelivery['NAME']?></label><?
									}
									?>
								</div>
								<div class="deliveryLocation" <?
								if($_SESSION['ORDER']['DELIVERY'] == 2){
									?> style="display: flex" <?
								}
								?>>
									<div class="deliveryLocationItem">
										<div class="field">
											<div class="name">Населенный пункт <span>(город, поселок)</span> <span class="star">*</span></div>
											<input type="text" name="CITY" value="<?=html($_SESSION['ORDER']['CITY'])?>">
										</div>
										<div class="field">
											<div class="name">Индекс <span class="star">*</span></div>
											<input type="text" name="INDEX" value="<?=html($_SESSION['ORDER']['INDEX'])?>">
										</div>
										<div class="field">
											<div class="name">Улица <span class="star">*</span></div>
											<input type="text" name="STREET" value="<?=html($_SESSION['ORDER']['STREET'])?>">
										</div>
									</div>
									<div class="deliveryLocationItem">
										<div class="field">
											<div class="name">Дом, корпус <span class="star">*</span></div>
											<input type="text" name="HOME" value="<?=html($_SESSION['ORDER']['HOME'])?>">
										</div>
										<div class="field">
											<div class="name">Квартира</div>
											<input type="text" name="KV" value="<?=html($_SESSION['ORDER']['KV'])?>">
										</div>
									</div>
								</div>
								<input type="submit" value="Далее" class="sub">
								<div class="note"><span class="star">*</span> обязательные для заполнения поля</div>
								<div class="clear"></div>
							</form>
						</div>
						<?
					}elseif($_GET['action'] == 'payment'){
						?>
						<div class="item">
							<form action="?action=payment" method="post">
								<div class="title">Способы оплаты</div>
								<div class="form2">
									<?
									foreach($this->Payment as $arPayment)
									{
										?><label><input type="radio" name="PAYMENT" value="<?=$arPayment['ID']?>"<?
										if($_SESSION['ORDER']['PAYMENT'] == $arPayment['ID']){
											?> checked<?
										}
										?>><?=$arPayment['NAME']?></label><?
									}
									?>
								</div>
								<input type="submit" value="Далее" class="sub">
								<div class="note"><span class="star">*</span> обязательные для заполнения поля</div>
								<div class="clear"></div>
							</form>
						</div>
						<?
					}elseif($_GET['action'] == 'result'){
						?>
						<div class="item">
							<form action="?action=result" method="post">
								<div class="title">Подтверждение заказа</div>
								<div class="form2">
									<div class="data">
										<table width="600">
											<?
											foreach ($this->Prop as $arArray) {
												?><tr>
												<td><?=$arArray[0]?></td>
												<td><?=html($arArray[1])?></td>
												</tr><?
											}
											?>
										</table>
									</div>
									<textarea name="COMMENT" placeholder="Напишите комментарий к заказу: Например, удобное время доставки"></textarea>
									<br><br>
								</div>
								<input type="submit" value="Оформить заказ" class="sub">
								<div class="clear"></div>
							</form>
						</div>
						<?
					} else{
						?>
						<div class="item">
							<form action="?action=contact" method="post">
								<div class="title">Контактные данные</div>
								<div class="form">
									<div class="field">
										<div class="name">Ф.И.О. <span class="star">*</span></div>
										<input name="NAME" type="text" value="<?=html($_SESSION['ORDER']['NAME'])?>">
									</div>
									<div class="field">
										<div class="name">Телефон <span class="star">*</span></div>
										<input name="PHONE" type="text" value="<?=html($_SESSION['ORDER']['PHONE'])?>">
									</div>
									<div class="field">
										<div class="name">E-mail <span class="star">*</span></div>
										<input name="EMAIL" type="text" value="<?=html($_SESSION['ORDER']['EMAIL'])?>">
									</div>
								</div>
								<input type="submit" value="Далее" class="sub">
								<div class="note"><span class="star">*</span> обязательные для заполнения поля</div>
								<div class="clear"></div>
							</form>
						</div>
						<?
					}
					?>

				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
