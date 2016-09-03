<?
function fieldType($nID)
{
	$arField = array('Строка', 'Текстовая область', 'Истина или ложь', 'Список', 'Файл', 'Связь', 'Скрытое');

	return $arField[$nID];
}

?>