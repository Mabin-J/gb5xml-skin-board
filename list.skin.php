<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

header('Content-Type: text/xml');

$showFields = Array(
    "wr_id",
    "is_notice",
    "num",
    "ca_name",
    "ca_name_href",
    "subject",
    "href",
    "comment_cnt",
    "icon_new",
    "icon_hot",
    "icon_file",
    "icon_link",
    "icon_secret",
    "icon_reply",
    "name",
    "datetime2",
    "wr_hit",
    "wr_good",
    "wr_nogood"
);

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<?xml-stylesheet type=\"text/xsl\" href=\"" . $board_skin_url . "/list.xsl.php?bo_table=" . $bo_table . "\"?>\n";
?>
<board_list>
    <title><?=$board['bo_subject']?></title>
    <writable><?=$write_href?"true":"false"?></writable>
    <perm_admin><?=$admin_href?"true":"false"?></perm_admin>
    <use_category><?=$is_category?"true":"false"?></use_category>
<?php
if($is_category){
?>
    <category_option><?=$category_option?></category_option>
<?php
}
?>
    <total_count><?=number_format($total_count)?></total_count>
    <use_rss><?=$rss_href?"true":"false"?></use_rss>
    <use_checkbox><?=$is_checkbox?"true":"false"?></use_checkbox>
    <use_good><?=$is_good?"true":"false"?></use_good>
    <use_nogood><?=$is_nogood?"true":"false"?></use_nogood>
    <bo_subject><?=$board['bo_subject']?></bo_subject>
    <bo_table><?=$bo_table?></bo_table>
    <page><?=$page?></page>
    <list>
<?php
foreach ($list as $item){
?>
        <item>
<?php
    foreach($showFields as $field){
        if(substr($field, 0, 4) == "icon"){
            $item[$field] = str_replace("\">", "\" />", $item[$field]);
        }
?>
            <<?=$field?>><?=$item[$field]?></<?=$field?>>
<?php
    }
?>
        </item>
<?php
}
?>
    </list>
    <write_pages><![CDATA[<?=$write_pages?>]]></write_pages>
    <search>
        <sfl><?=$sfl?></sfl>
        <stx><?=stripslashes($stx)?></stx>
        <spt><?=$spt?></spt>
        <sca><?=$sca?></sca>
        <sst><?=$sst?></sst>
        <sod><?=$sod?></sod>
    </search>
</board_list>
