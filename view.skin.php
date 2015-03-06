<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');


header('Content-Type: text/xml');

$showFieldsPost = Array(
    "ca_name",
    "name",
    "wr_datetime",
    "wr_hit",
    "wr_comment",
    "wr_good",
    "wr_nogood"
);

$showFieldsHrefs = Array(
    "list_href",
    "prev_href",
    "next_href",
    "update_href",
    "delete_href",
    "copy_href",
    "move_href",
    "search_href",
    "reply_href",
    "write_href",
    "scrap_href",
    "good_href",
    "nogood_href"
);

if($good_href)
    $good_href = $good_href.'&amp;'.$qstr;
if($nogood_href)
    $nogood_href = $nogood_href.'&amp;'.$qstr;
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<?xml-stylesheet type=\"text/xsl\" href=\"" . $board_skin_url . "/view.xsl.php?bo_table=" . $bo_table . "\"?>\n";
?>
<board_view>
    <board_info>
        <title><?=$board['bo_subject']?></title>
        <use_good><?=$board['bo_use_good']?"true":"false"?></use_good>
        <use_nogood><?=$board['bo_use_nogood']?"true":"false"?></use_nogood>
        <use_signature><?=$is_signature?"true":"false"?></use_signature>
        <bo_subject><?=$board['bo_subject']?></bo_subject>
        <bo_table><?=$bo_table?></bo_table>
        <use_ip_view><?=$is_ip_view?"true":"false"?></use_ip_view>
    </board_info>
<?php
foreach($showFieldsPost as $field){
?>
    <<?=$field?>><?=$view[$field]?></<?=$field?>>
<?php
}
if($is_ip_view){
?>
    <ip><?=$ip?></ip>
<?php
}
?>
    <wr_subject><?=cut_str(get_text($view['wr_subject']), 70)?></wr_subject>
    <wr_id><?=$view['wr_id']?></wr_id>
    <contents><?=get_view_thumbnail($view['content'])?></contents>
<?php
if($is_signature){
?>
    <signature><?=$signature?></signature>
<?php
}
?>
    <files>
<?php
for($i = 0; $i < count($view['file']); $i++){
    $file = $view['file'][$i];
    if (isset($view['file'][$i]['source']) && $view['file'][$i]['source']) {
?>
        <file>
            <content><?=$file['content']?></content>
            <href><?=$file['href']?></href>
            <source><?=$file['source']?></source>
<?php
$tmpView = get_view_thumbnail($file['view']);
$tmpView = preg_replace("/<img[^<>]+/", "$0 /", $tmpView);
//$tmpView = str_replace("<img", "<view_img", $tmpView);
//$tmpView = str_replace("<a", "<view_a", $tmpView);
//$tmpView = str_replace("</a", "</view_a", $tmpView);
?>
            <view><?=$tmpView?></view>
            <download><?=$file['download']?></download>
            <size><?=$file['size']?></size>
            <datetime><?=$file['datetime']?></datetime>
        </file>
<?php
    }
}
?>
    </files>
    <links>
<?php
for($i = 0; $i < count($view['link']); $i++){
    $link = $view['link'][$i];
    if($link){
?>
        <link>
            <name><?=$link?></name>
            <href><?=$view['link_href'][$i]?></href>
            <hit><?=$view['link_hit'][$i]?></hit>
        </link>
<?php
    }
}
?>
    </links>
<?php include_once "./view_comment.php"?>
    <hrefs>
<?php
foreach($showFieldsHrefs as $field){
?>
        <<?=$field?>><?=$$field ?></<?=$field?>>
<?php
}
?>
    </hrefs>
    <search>
        <sfl><?=$sfl?></sfl>
        <stx><?=stripslashes($stx)?></stx>
        <spt><?=$spt?></spt>
        <sca><?=$sca?></sca>
        <sst><?=$sst?></sst>
        <sod><?=$sod?></sod>
    </search>
<?php
if($c_id || $page || $w == 'cu'){
?>
    <other_params>
<?php
    if($c_id){
?>
        <c_id><?=$c_id?></c_id>
<?php
    }
    if($page){
?>
        <page><?=$page?></page>
<?php
    }
    if($w){
?>
        <w><?=$w?></w>
<?php
    }
    if($w == 'cu') {
        $sql = " select wr_id, wr_content from $write_table where wr_id = '$c_id' and wr_is_comment = '1' ";
        $cmt = sql_fetch($sql);
        $c_wr_content = $cmt['wr_content'];
?>
        <c_wr_content><?=$c_wr_content?></c_wr_content>
<?php
    }
?>
    </other_params>
<?php
}
?>
</board_view>
