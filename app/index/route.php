<?php
namespace app\route;
use think\Route;
use think\Request;

Route::rule('/','index/Index/index');

Route::rule(['link' => ['index/Index/link',['ext'=>'html'],[]]]);


Route::rule('/sitemap.xml','index/Index/sitemap');

Route::rule(['xml/:id' => ['index/Index/xml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);

Route::rule(['tagXml/:id' => ['index/Index/tagXml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);

Route::rule('/baiduSitemapPc.xml','index/Index/baiduSitemapPc');

Route::rule(['pcXml/:id' => ['index/Index/pcXml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);

    
