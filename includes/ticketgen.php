<?php
include_once(dirname(__FILE__) . "/fpdf/fpdf.php");
include_once(dirname(__FILE__) . "/fpdf/fpdi.php");

class ConmanPDFOverloaded extends FPDI
{
	public function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5)
	{
		$wide = $baseline;
		$narrow = $baseline / 3 ; 
		$gap = $narrow;

		$barChar['0'] = 'nnnwwnwnn';
		$barChar['1'] = 'wnnwnnnnw';
		$barChar['2'] = 'nnwwnnnnw';
		$barChar['3'] = 'wnwwnnnnn';
		$barChar['4'] = 'nnnwwnnnw';
		$barChar['5'] = 'wnnwwnnnn';
		$barChar['6'] = 'nnwwwnnnn';
		$barChar['7'] = 'nnnwnnwnw';
		$barChar['8'] = 'wnnwnnwnn';
		$barChar['9'] = 'nnwwnnwnn';
		$barChar['A'] = 'wnnnnwnnw';
		$barChar['B'] = 'nnwnnwnnw';
		$barChar['C'] = 'wnwnnwnnn';
		$barChar['D'] = 'nnnnwwnnw';
		$barChar['E'] = 'wnnnwwnnn';
		$barChar['F'] = 'nnwnwwnnn';
		$barChar['G'] = 'nnnnnwwnw';
		$barChar['H'] = 'wnnnnwwnn';
		$barChar['I'] = 'nnwnnwwnn';
		$barChar['J'] = 'nnnnwwwnn';
		$barChar['K'] = 'wnnnnnnww';
		$barChar['L'] = 'nnwnnnnww';
		$barChar['M'] = 'wnwnnnnwn';
		$barChar['N'] = 'nnnnwnnww';
		$barChar['O'] = 'wnnnwnnwn'; 
		$barChar['P'] = 'nnwnwnnwn';
		$barChar['Q'] = 'nnnnnnwww';
		$barChar['R'] = 'wnnnnnwwn';
		$barChar['S'] = 'nnwnnnwwn';
		$barChar['T'] = 'nnnnwnwwn';
		$barChar['U'] = 'wwnnnnnnw';
		$barChar['V'] = 'nwwnnnnnw';
		$barChar['W'] = 'wwwnnnnnn';
		$barChar['X'] = 'nwnnwnnnw';
		$barChar['Y'] = 'wwnnwnnnn';
		$barChar['Z'] = 'nwwnwnnnn';
		$barChar['-'] = 'nwnnnnwnw';
		$barChar['.'] = 'wwnnnnwnn';
		$barChar[' '] = 'nwwnnnwnn';
		$barChar['*'] = 'nwnnwnwnn';
		$barChar['$'] = 'nwnwnwnnn';
		$barChar['/'] = 'nwnwnnnwn';
		$barChar['+'] = 'nwnnnwnwn';
		$barChar['%'] = 'nnnwnwnwn';

		$this->SetFont('Arial','',10);
		$this->Text($xpos, $ypos + $height + 4, $code);
		$this->SetFillColor(0);

		$code = '*'.strtoupper($code).'*';
		for ($i = 0; $i < strlen($code); $i++) {
			$char = $code[$i];
			if (!isset($barChar[$char])) {
				$this->Error('Invalid character in barcode: '.$char);
			}
			$seq = $barChar[$char];
			for ($bar = 0; $bar < 9; $bar++) {
				if ($seq[$bar] == 'n') {
					$lineWidth = $narrow;
				} else {
					$lineWidth = $wide;
				}
				
				if  ($bar % 2 == 0) {
					$this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
				}
				
				$xpos += $lineWidth;
			}
			$xpos += $gap;
		}
	}
}

class TicketGen 
{
	private $_pdf = null;
	
	public function __construct()
	{
		$this->_pdf =& new ConmanPDFOverloaded();
		$pagecount = $this->_pdf->setSourceFile(Settings::getRoot() . '/templates/default/ticket.pdf'); 
		$tplidx = $this->_pdf->importPage(1); 
		$this->_pdf->addPage(); 
		$this->_pdf->useTemplate($tplidx); 		
	}
	
	public function addBarCode($xpos, $ypos, $code, $baseline=0.5, $height=5)
	{
		$this->_pdf->Code39($xpos, $ypos, $code, $baseline, $height);
	}
	
	public function generate()
	{
		$this->_pdf->Output('biljett.pdf', 'D');
	}
}
