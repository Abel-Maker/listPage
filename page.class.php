<?php

class MyPageUrl{

	private $totalNum;			//总条数
	private $perpageNum;		//每页显示条数	
	private $pageNow;			//当前页页码
	private $url;				//当前url

	
	//页码显示
	private $pageStyle; //页码样式，提供3种样式
	private $prePage;		//页码前偏移量
	private $floPage;		//页码后偏移量
	private $skipStyle;		//手动跳转，0为手动输入页码，1为下拉菜单选择页码

	//页码文字
	//style2&style3
	private $firstFonts = "首页";
	private $lastFonts = "末页";

	private $nextFonts = "下一页 >";		
	private $preFonts = "< 上一页";

	//前n页，后n页
	private $pn = 10;
	private $pn_fonts = "前10页";
	private $fn = 10;
	private $fn_fonts = "后10页";

	//展现的页码
	private $pageShow;

	//构造函数
	function __construct($totalNum,$perpageNum,$prePage,$preFonts,$floPage,$nextFonts,$p,$skipStyle,$pageStyle){
	
		$this->totalNum = $totalNum;
		$this->perpageNum = $perpageNum;
		$this->prePage = $prePage;
		$this->floPage = $floPage;
		$this->skipStyle = $skipStyle;
		$this->pageStyle = $pageStyle;

		$this->getPageNow($p);

		$this->totalPage = ceil($totalNum / $this->perpageNum); //总页数
		$this->firstRow = $this->perpageNum * ($this->pageNow-1) + 1;//当前页第一条是总条数中第几条

		$this->pageShow = "";
		$this->getUrl();
		//前偏移量处理
		$this->preOffset();

		//后偏移量的处理
		$this->floOffset();
	
		//其他页码信息
		$this->getOtherInfo();
	}
			

	/************定义__toString方法,把对象解析成字符串******/
	public function __toString(){
	
		return $this->pageShow;
	}

	/************获得当前页页码,$p用来接收$_GET['p']*******/
	public function getPageNow($p){
	
		if(!isset($p)){
			
			$this->pageNow = 1;
		}else if($p>0){
			
			$this->pageNow = $p;	
		}else{
		
			die("page number error");
		}

		return $this->pageNow;
	}


    /***********************设置当前页面链接***************/
    public function getUrl(){

        $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		//判断是否带参数
		if(strpos($url,"?") === false){ //不带参数

			return $this->url = $url;
		}else{ //带参数
		
			$url = explode("?",$url);
			$param = $url[1];

			//判断是否有多个参数
			if(strpos($param,"&") === false){ //只有一个参数
	
				//判断参数是否为p
				if(strpos($param,"p=") === false){ //不含参数p
				
					//合并url
					$url = implode("?",$url);	
				}else{
				
					$url = $url[0];
				}
				
			}else{ //多个参数
			
				$param = explode("&",$param);

				//遍历参数数组
				foreach($param as $k=>$v){

					if(strpos($v,"p=") === false){

						continue;
					}else{
			
						//当含有参数p时，把它从数组中删除
						unset($param[$k]);
					}
				}

				//删除参数p之后组合数组
				$param = implode("&",$param);
				$url = implode("?",$url);
			}

			return $this->url = $url;
		}
	}

	/************************前偏移量处理********************/
	public function preOffset(){
	
		//前偏移量的处理
		if($this->pageNow!=1 && ($this->pageNow - $this->prePage -1 <= 1)){
					
			$this->pageShow .= "<a class=\"pagenum\" href=\"".$this->url."?p=".($this->pageNow-1)."\">".($preFonts == ""?$this->preFonts:$preFonts)."</a>";
			
			for($i=1;$i<=$this->pageNow-1;$i++){		

				$this->pageShow .= "<a class=\"pagenum\" href=\"".$this->url."?p=".$i."\">".$i."</a>";	
			}

		}else if($this->pageNow - $this->prePage -1 > 1){ //pageNow至少大于2时才会出现"1..."
			
			//样式1.加上'首页'
			if($this->pageStyle == 2 || $this->pageStyle == 3){
				
				$this->pageShow .= "<a id=\"first_page\" class=\"pagenum\" href=\"".$this->url."?p=1\">".$this->firstFonts."</a>";

				//style3.前n页
				if($this->pageStyle == 3){
				
					if($this->pageNow>$this->pn){
					
						$this->pageShow .= "<a id=\"first_page\" class=\"pagenum\" href=\"".$this->url."?p=".($this->pageNow-$this->pn)."\">".$this->pn_fonts."</a>";
					}
				}
			}
			
			
			$this->pageShow .= "<a class=\"pagenum\" href=\"".$this->url."?p=".($this->pageNow-1)."\">".($preFonts == ""?$this->preFonts:$preFonts)."</a>";
			
			//样式2.加上第一页'1'...
			if($this->pageStyle == 1){
				$this->pageShow .=  "<a class=\"pagenum\" href=\"".$this->url."\">1</a><a class=\"pagenum\" href=\"".$this->url."?p=".($this->pageNow-$this->prePage-1)." \" title=\"第".($this->pageNow-$this->prePage-1)."页\">…</a>";
			}

			for($i=$this->prePage;$i>=1;$i--){		

				$this->pageShow .= "<a class=\"pagenum\" href=\"".$this->url."?p=".($this->pageNow-$i)."\">".($this->pageNow-$i)."</a>";	
			}
		}
	}

	/**********************后偏移量处理***************************/
	public function floOffset(){
	
		if($this->totalPage > $this->floPage){ //总页数大于后偏移量时
		
			for($i=0;$i<=$this->floPage;$i++){
			
				$page = $this->pageNow+$i;
				
				if($page<=$this->totalPage){

					$this->pageShow .= "<a class=\"pagenum\" href=\"".$this->url."?p=".$page."\">".$page."</a>";
				}
			}

			if($this->pageNow < $this->totalPage){

				
				//当前页+后偏移量+1小于总页数时出现"..."
				if(($this->pageNow+$this->floPage+1)<$this->totalPage){
			
					//样式1.显示'...'
					if($this->pageStyle == 1){
						$this->pageShow .= "<a class=\"pagenum\" href=\"".$this->url."?p=".($page+1)."\" title=\"第".($page+1)."页\">…</a>";
					}
				}
				
				
				//当前页+后偏移量+1小于等于总页数时出现最后一页的快捷标签
				if(($this->pageNow+$this->floPage+1)<=$this->totalPage){
				
					//最后一页
					//样式1.始终出现'最后一页页码'
					if($this->pageStyle == 1){
						$this->pageShow .= "<a class=\"pagenum\" href=\"".$this->url."?p=".$this->totalPage."\" title=\"总共".$this->totalPage."页\">".$this->totalPage."</a>";
					}
				}						

				$this->pageShow .= "<a class=\"pagenum\" href=\"".$this->url."?p=".($this->pageNow+1)."\">".($nextFonts == ""?$this->nextFonts:$nextFonts)."</a>"; //当实例化对象时用户传递的文字为空时则调用类预设的"下一页",否则输出用户传递的值

				//style3.加上后n页
				if($this->pageStyle == 3){
				
					if(($this->pageNow+10)<$this->totalPage){
					
						$this->pageShow .= "<a id=\"first_page\" class=\"pagenum\" href=\"".$this->url."?p=".($this->pageNow+$this->fn)."\">".$this->fn_fonts."</a>";
					}
				}

				//显示'末页'
				if($this->pageStyle == 2 || $this->pageStyle == 3){
				
					if(($this->pageNow+$this->floPage+1)<$this->totalPage){

						$this->pageShow .= "<a class=\"pagenum\" href=\"".$this->url."?p=".$this->totalPage."\">末页</a>";
					}
				}

			}else if($this->pageNow > $this->totalPage){
			
				die("超出页码范围");
			}

		}else{ //总页数小于后偏移量时
			
			for($i=0;$i<$this->totalPage;$i++){
			
				$page = $this->pageNow+$i;
				$this->pageShow .= "<a class=\"pagenum\" href=\"".$this->url."?p=".$page."\">".$page."</a>";
			}
		}
	}

	/********************其它页面信息***********************/
	public function getOtherInfo(){
	
		$this->pageShow .= "&nbsp;跳转至 ";

		//跳转类型
		if($this->skipStyle =="" ){ //不加跳转
		
			$this->pageShow .= "";
		}else if($this->skipStyle == 1){ //输入框
		
			$this->pageShow .= "<input id=\"skip\" type=\"text\" value=\"".$this->pageNow."\">";
		
			$this->pageShow .= "<button id=\"go\">GO</button>";
		}else if($this->skipStyle == 2){ //下拉菜单
		
			//选择下拉菜单自动跳转
			$this->pageShow .= "<select id=\"select_page\" onchange=\"location.href=this.options[this.selectedIndex].value;\" >";
			
			for($i=1;$i<=$this->totalPage;$i++){
			
				$this->pageShow .= "<option value=\"".$this->url."?p=".$i."\"";  
				
				//下拉菜单默认显示当前页
				if($i == $this->pageNow){
				
					$this->pageShow .= " selected";
				}
				
				$this->pageShow .= ">".$i."</option>";
			}
			
			$this->pageShow .= "</select>";
		}

		$this->pageShow .= "&nbsp;&nbsp;当前第".$this->pageNow."页";
		$this->pageShow .= "/共".$this->totalPage."页";
		$this->pageShow .= "&nbsp;共".$this->totalNum."条";
	}
}