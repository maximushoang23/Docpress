<?php
class DOCTCPDF extends TCPDF {
public $footertxt;
public $footeronfirstpg;
public $headeronfirstpg;
public function Header() {
		if ($this->header_xobjid === false) {
			// start a new XObject Template
			$this->header_xobjid = $this->startTemplate($this->w, $this->tMargin);
			$headerfont = $this->getHeaderFont();
			$headerdata = $this->getHeaderData();
			$this->y = $this->header_margin;
			if ($this->rtl) {
				$this->x = $this->w - $this->original_rMargin;
			} else {
				$this->x = $this->original_lMargin;
			}
			if (($headerdata['logo']) AND ($headerdata['logo'] != K_BLANK_IMAGE)) {
				$imgtype = TCPDF_IMAGES::getImageFileType(K_PATH_IMAGES.$headerdata['logo']);
				if (($imgtype == 'eps') OR ($imgtype == 'ai')) {
					$this->ImageEps(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
				} elseif ($imgtype == 'svg') {
					$this->ImageSVG(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
				} else {
					$this->Image(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
				}
				$imgy = $this->getImageRBY();
			} else {
				$imgy = $this->y;
			}
			$cell_height = $this->getCellHeight($headerfont[2] / $this->k);
			// set starting margin for text data cell
			if ($this->getRTL()) {
				$header_x = $this->original_rMargin + ($headerdata['logo_width'] * 1.1);
			} else {
				$header_x = $this->original_lMargin + ($headerdata['logo_width'] * 1.1);
			}
			$cw = $this->w - $this->original_lMargin - $this->original_rMargin - ($headerdata['logo_width'] * 1.1);
			$this->SetTextColorArray($this->header_text_color);
			
			$headersubArr = explode("\n", $headerdata['string']);
			$link = ( !empty( $headersubArr[1] ) ) ? $headersubArr[1] : '';
			//link in header	
			$this->SetFont($headerfont[0], '', $headerfont[2] - 2);
			$subtitle = ( !empty( $headersubArr[0] ) ) ? $headersubArr[0] : '';
			$this->MultiCell($cw, $cell_height, $link, 0, 'R', 0, 1, '', '', true, 0, false, true, 0, '', false);
			// header title
			$this->SetFont($headerfont[0], 'B', $headerfont[2] + 4);
			$this->SetX($header_x);
			$this->MultiCell($cw, $cell_height, $headerdata['title'], 0, 'R', 0, 1, '', '', true, 0, false, true, 0, '', false);
			// header string
			$this->SetFont($headerfont[0], '', $headerfont[2]);
			$this->SetX($header_x);
			$this->MultiCell($cw, $cell_height, $subtitle, 0, 'R', 0, 1, '', '', true, 0, false, true, 0, 'T', false);
			// print an ending header line
			$this->SetLineStyle(array('width' => 0.85 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $headerdata['line_color']));
			$this->SetY((2.835 / $this->k) + max($imgy, $this->y));
			if ($this->rtl) {
				$this->SetX($this->original_rMargin);
			} else {
				$this->SetX($this->original_lMargin);
			}
			$this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin), 0, '', 'T', 0, 'C');
			$this->endTemplate();
		}
		// print header template
		$x = 0;
		$dx = 0;
		if (!$this->header_xobj_autoreset AND $this->booklet AND (($this->page % 2) == 0)) {
			// adjust margins for booklet mode
			$dx = ($this->original_lMargin - $this->original_rMargin);
		}
		if ($this->rtl) {
			$x = $this->w + $dx;
		} else {
			$x = 0 + $dx;
		}
		//on first page check if header is enabled -> added for documentor
		if( $this->PageNo() == 1 ) {
			if( $this->headeronfirstpg == 1 )
				$this->printTemplate($this->header_xobjid, $x, 0, 0, 0, '', '', false);
		} else {
			$this->printTemplate($this->header_xobjid, $x, 0, 0, 0, '', '', false);
		}
		if ($this->header_xobj_autoreset) {
			// reset header xobject template at each page
			$this->header_xobjid = false;
		}
	}

	/**
	 * This method is used to render the page footer.
	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
	 * @public
	 */
	public function Footer() {
		$cur_y = $this->y;
		$this->SetTextColorArray($this->footer_text_color);
		//set style for cell border
		$line_width = (0.85 / $this->k);
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));
		//print document barcode
		$barcode = $this->getBarcode();
		if (!empty($barcode)) {
			$this->Ln($line_width);
			$barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin) / 3);
			$style = array(
				'position' => $this->rtl?'R':'L',
				'align' => $this->rtl?'R':'L',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false,
				'text' => false
			);
			$this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
		}
		$w_page = isset($this->l['w_page']) ? $this->l['w_page'].' ' : '';
		if (empty($this->pagegroups)) {
			$pagenumtxt = $w_page.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
		} else {
			$pagenumtxt = $w_page.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
		}
		$this->SetY($cur_y);
		//Print page number
		//on first page check if footer is enabled -> added for documentor
		if( $this->PageNo() == 1 ) {
			if( $this->footeronfirstpg == '1' ) {
				if ($this->getRTL()) {
					$this->SetX($this->original_rMargin);
					$this->Cell(0, 0, $this->footertxt.' '.$pagenumtxt, '', 0, 'L');
				} else {
					$this->SetX($this->original_lMargin);
					$this->Cell(0, 0, $this->footertxt.' '.$pagenumtxt, '', 0, 'C');
				}
			}
		} else {
			if ($this->getRTL()) {
				$this->SetX($this->original_rMargin);
				$this->Cell(0, 0, $this->footertxt.' '.$pagenumtxt, '', 0, 'L');
			} else {
				$this->SetX($this->original_lMargin);
				$this->Cell(0, 0, $this->footertxt.' '.$pagenumtxt, '', 0, 'C');
			}
		}
	}
}

?>
