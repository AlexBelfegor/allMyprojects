<?php

class shopBwexportPlugin extends shopPlugin
{
    private $templatepaths = array();

    public function reports()
    {
		$menu = '<li><a href="#/bworder/">Отчет для заказа</a>
		<script>
		$(function(){
			$.reports.bworderAction = function () {
				this.setActiveTop("bworder");
				$("#reportscontent").load("?plugin=bwexport&action=report"+this.getTimeframeParams());
			};

			$.reports.bworderSendedAction = function () {
				this.setActiveTop("bworder");
				$("#reportscontent").load("?plugin=bwexport&action=report&sended=1"+this.getTimeframeParams());
			};

			$.reports.bworderFilterAction = function () {
				var reportshash = location.hash.replace("#/bworder/filter/","");
				this.setActiveTop("bworder");
				$("#reportscontent").load("?plugin=bwexport&action=report&filter=1&hash="+reportshash+this.getTimeframeParams());
			};
		});
		</script>
		</li>';

        $result['menu_li'] = $menu;
        //$result['menu_li'] = "<li><a href='?plugin=bwexport&action=report'>Отчет для заказа</a></li>";

	    return $result;
    }

    /*private functions*/
    private function getTemplatePaths()
    {
        if (!$this->templatepaths) {
            $this->templatepaths = array(
                'original' => $this->path . '/templates/PopupTemplate.html'
            );
        }
        return $this->templatepaths;
    }

    public function getTemplatePath()
    {
        foreach ($this->getTemplatePaths() as $filepath) {
            if (file_exists($filepath)) {
                return $filepath;
            }
        }
        return '';
    }

}

