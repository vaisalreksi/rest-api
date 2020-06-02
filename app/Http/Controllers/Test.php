<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use \PhpOffice\PhpWord\PhpWord;
use Illuminate\Http\Request;
use \PhpOffice\PhpWord\IOFactory;

class Test extends Controller
{
	public function word2()
	{
		$wordTest = new PhpWord();

	  $newSection = $wordTest->addSection();

	  $desc1 = "The Portfolio details is a very useful feature of the web page. You can establish your archived details and the works to the entire web community. It was outlined to bring in extra clients, get you selected based on this details.";

	  $newSection->addText('http://www.2mdc.com/PHPDOCX/simpleHTML_Image.html', array('isFile' => true, 'downloadImages' => true));

	  $objectWriter = IOFactory::createWriter($wordTest, 'Word2007');
	  try {
	      $objectWriter->save(storage_path('TestWordFile.docx'));
	  } catch (Exception $e) {
	  }

	  return response()->download(storage_path('TestWordFile.docx'));
	}

	public function word()
	{
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		// $phpWord->addParagraphStyle('Heading2', array('alignment' => 'center'));
		$section = $phpWord->addSection();

		// $html = '<style>.test{ color:red; }</style>';
		// $html .= '<h1 class="test">Adding element via HTML</h1>';
		// $html .= '<p>Some well-formed HTML snippet needs to be used</p>';
		// $html .= '<p>With for example <strong>some<sup>1</sup> <em>inline</em> formatting</strong><sub>1</sub></p>';
		// $html .= '<p>A link to <a href="http://phpword.readthedocs.io/" style="text-decoration: underline">Read the docs</a></p>';
		// $html .= '<p lang="he-IL" style="text-align: right; direction: rtl">היי, זה פסקה מימין לשמאל</p>';
		// $html .= '<p style="margin-top: 240pt;">Unordered (bulleted) list:</p>';
		// $html .= '<ul><li>Item 1</li><li>Item 2</li><ul><li>Item 2.1</li><li>Item 2.1</li></ul></ul>';
		// $html .= '<p style="margin-top: 240pt;">1.5 line height with first line text indent:</p>';
		// $html .= '<p style="text-align: justify; text-indent: 70.9pt; line-height: 150%;">Lorem ipsum dolor sit amet, <strong>consectetur adipiscing elit</strong>, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>';
		// $html .= '<h2 style="align: center">centered title</h2>';
		// $html .= '<p style="margin-top: 240pt;">Ordered (numbered) list:</p>';
		// $html .= '<ol>
    //             <li><p style="font-weight: bold;">List 1 item 1</p></li>
    //             <li>List 1 item 2</li>
    //             <ol>
    //                 <li>sub list 1</li>
    //                 <li>sub list 2</li>
    //             </ol>
    //             <li>List 1 item 3</li>
    //         </ol>
    //         <p style="margin-top: 15px;">A second list, numbering should restart</p>
    //         <ol>
    //             <li>List 2 item 1</li>
    //             <li>List 2 item 2</li>
    //             <li>
    //                 <ol>
    //                     <li>sub list 1</li>
    //                     <li>sub list 2</li>
    //                 </ol>
    //             </li>
    //             <li>List 2 item 3</li>
    //             <ol>
    //                 <li>sub list 1, restarts with a</li>
    //                 <li>sub list 2</li>
    //             </ol>
    //         </ol>';
		// $html .= '<p style="margin-top: 240pt;">List with formatted content:</p>';
		// $html .= '<ul>
		//                 <li>
		//                     <span style="font-family: arial,helvetica,sans-serif;">
		//                         <span style="font-size: 16px;">big list item1</span>
		//                     </span>
		//                 </li>
		//                 <li>
		//                     <span style="font-family: arial,helvetica,sans-serif;">
		//                         <span style="font-size: 10px; font-weight: bold;">list item2 in bold</span>
		//                     </span>
		//                 </li>
		//             </ul>';
		// $html .= '<p style="margin-top: 240pt;">A table with formatting:</p>';
		// $html .= '<table align="center" style="width: 50%; border: 6px #0000FF double;">
		//                 <thead>
		//                     <tr style="background-color: #FF0000; text-align: center; color: #FFFFFF; font-weight: bold; ">
		//                         <th style="width: 50pt">header a</th>
		//                         <th style="width: 50">header          b</th>
		//                         <th style="background-color: #FFFF00; border-width: 12px"><span style="background-color: #00FF00;">header c</span></th>
		//                     </tr>
		//                 </thead>
		//                 <tbody>
		//                     <tr><td style="border-style: dotted;">1</td><td colspan="2">2</td></tr>
		//                     <tr><td>This is <b>bold</b> text</td><td></td><td>6</td></tr>
		//                 </tbody>
		//             </table>';
		// $html .= '<p style="margin-top: 240pt;">Table inside another table:</p>';
		// $html .= '<table align="center" style="width: 80%; border: 6px #0000FF double;">
		//     <tr><td>
		//         <table style="width: 100%; border: 4px #FF0000 dotted;">
		//             <tr><td>column 1</td><td>column 2</td></tr>
		// 				</table>
		//     </td></tr>
		//     <tr><td style="text-align: center;">Cell in parent table</td></tr>
		// </table>';

		$html = '<table style="width:100%;border-spacing:0px;font-size:12px;border: 1px solid;" align="center">
							<tbody>
								<tr style="page-break-before: always;">
									<td rowspan="2" align="center" style="font-size: 14px !important;padding: 23px !important;border: 1px solid !important;">
										<b>SURAT PERINTAH KERJA (SPK)</b>
									</td>
									<td style="min-width: 400px;font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">
										SATUAN KERJA PPK : Penerbangan
									</td>
								</tr>
								<tr style="page-break-before: always;">
									<td style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">
										NOMOR SPK : 10/SPK/PU/DNP/XI/2017<br/>TANGGAL : 27 November 2018
									</td>
								</tr>
								<tr style="page-break-before: always;">
									<td style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">
										<p id="page" class="nomargin">Halaman 1 dari 2</p>
									</td>
									<td style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;"></td>
								</tr>
								<tr style="page-break-before: always;">
									<td style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">
										PAKET PEKERJAAN : PENGEMBANGAN WEBSITE AIM INDONESIA
									</td>
									<td style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">
										NOMOR DAN TANGGAL DOKUMEN PENGADAAN : 10/DP/PU/DNP/XI/2017 27 Februari 2018
									</td>
								</tr>
								<tr style="page-break-before: always;">
									<td colspan="2" style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">
										SUMBER DANA : Dibebankan atas PNBP Direktorat Navigasi Penerbangan Tahun Anggaran
		2017 untuk mata anggaran kegiatan Belanja Modal Peralatan dan Mesin
									</td>
								</tr>
								<tr style="page-break-before: always;">
									<td colspan="2" style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">
										TANGGAL MULAI : 27 November 2018
									</td>
								</tr>
								<tr style="page-break-before: always;">
									<td colspan="2" style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">
										WAKTU PELAKSANAAN PEKERJAAN : 30 (Tiga puluh) hari kalender dan pekerjaan harus sudah selesai pada tanggal 26 Desember 2018
									</td>
								</tr>
								<tr style="page-break-before: always;">
									<td colspan="2" align="center" style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">
										NILAI PEKERJAAN
									</td>
								</tr>
								<tr style="page-break-before: always;">
									<td colspan="2" rowspan="" align="center" style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;"><br/></td>
								</tr>
							</tbody>
						</table><br/>';
		// 	$html.='<table style="margin-top: -22px !important;font-size: 12px !important;width:100%;border-spacing:0px;border: 1px solid;">
		// 						<tr style="page-break-before: always;">
		// 							<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;">No</td>
		// 							<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;">Uraian Kegiatan</td>
		// 							<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;">Volume</td>
		// 							<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;">Satuan</td>
		// 							<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;" colspan="2">Harga Satuan</td>
		// 							<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;" colspan="2">Harga Total</td>
		// 						</tr>
		// 						<tr style="page-break-before: always;">
		// 							<td style="padding: 5px !important;border: 1px solid !important;">1</td>
		// 							<td style="padding: 5px !important;border: 1px solid !important;">Penyedia hardware</td>
		// 							<td style="padding: 5px !important;border: 1px solid !important;">5</td>
		// 							<td style="padding: 5px !important;border: 1px solid !important;">paket</td>
		// 							<td style="padding: 5px !important;border: 1px solid !important;">RP</td>
		// 							<td style="padding: 5px !important;border: 1px solid !important;">1000000</td>
		// 							<td style="padding: 5px !important;border: 1px solid !important;">RP</td>
		// 							<td style="padding: 5px !important;border: 1px solid !important;">5000000</td>
		// 						</tr>
		//
		// 						<tr style="page-break-before: always;">
		// 							<td style="padding: 5px !important;border: 1px solid !important;">2</td>
		// 							<td style="padding: 5px !important;border: 1px solid !important;">Instalasi os</td>
		// 							<td style="padding: 5px !important;border: 1px solid !important;">1</td>
		// 							<td style="padding: 5px !important;border: 1px solid !important;">license</td>
		// 								<td colspan="4" style="padding: 5px !important;border: 1px solid !important;text-align: center !important;">Include dalam pengadaan modul</td>
		// 						</tr>
		// 					</table>';
		// 	$html .='<br/>
		// 					<span>
		// 						<b>INSTRUKSI KEPADA PENYEDIA</b> : Penagihan hanya dapat dilakukan setelah penyelesaian
		// pekerjaan yang diperintahkan dalam SPK ini dan dibuktikan dengan Berita Acara Serah Terima.
		// 					</span>';
		// 	$html .='<table style="margin-top: 50px !important;width:100%;font-size: 12px !important;">
		// 						<tr style="page-break-before: always;">
		// 							<td align="center" style="border: 0px !important;">
		// 								Untuk Dan Atas Nama<br/>
		// 								<b>DIREKTORAT NAVIGASI PENERBANGAN</b> <br/>
		// 								<b>Pejabat Pembuat Komitmen</b> <br/>
		// 							</td>
		// 							<td align="center" style="border: 0px !important;">
		// 								Untuk dan atas nama Penyedia Barang/Jasa , <br/>
		// 								<b>PT. ELECTRONIC DATA INTERCHANGE INDONESIA</b>
		// 							</td>
		// 						</tr>
		// 						<tr style="page-break-before: always;">
		// 							<td align="center" height="200" style="border: 0px !important;">
		// 								<p style="margin-bottom:-14px;"> <b>BAYU SEKTI AJI</b> </p><br/>
		// 								.............................................<br/>
		// 								<b>Penata Tk.I (III/d)</b> <br/>
		// 								<b>NIP. 19861021 200812 1 002</b>
		// 							</td>
		//
		// 							<td align="center" height="200" style="border: 0px !important;">
		// 							<p style="margin-bottom:-14px;"> <b>E. HELMI WANTONO</b> </p><br/>
		// 							..................................................<br/>
		// 							<b>Direktur Utama</b> <br/>
		// 							</td>
		// 						</tr>
		// 					</table>';

							$html2 = '<table style="width: 50%; border: 6px #0000FF solid;">'.
            '<thead>'.
                '<tr style="background-color: #FF0000; text-align: center; color: #FFFFFF; font-weight: bold; ">'.
                    '<th>a</th>'.
                    '<th>b</th>'.
                    '<th>c</th>'.
                '</tr>'.
            '</thead>'.
            '<tbody>'.
                '<tr><td>1</td><td colspan="2">2</td></tr>'.
                '<tr><td>4</td><td>5</td><td>6</td></tr>'.
            '</tbody>'.
         '</table>';

				 $html3 = '<div>'.
				 						'<div id="spk-page" style="width:100%;">'.
											'<table style="width:100%;border-spacing:0px;font-size:12px;border: 1px #000 solid;" align="center">'.
												'<tr style="page-break-before: always;">'.
													'<td rowspan="2" align="center" style="font-size: 14px !important;padding: 23px !important;">'.
														'<b>SURAT PERINTAH KERJA (SPK)</b>'.
													'</td>'.
													'<td style="min-width: 400px;font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">'.
														'SATUAN KERJA PPK : Penerbangan'.
													'</td>'.
												'</tr>'.
											'<tr style="page-break-before: always;">'.
												'<td style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">'.
													'NOMOR SPK : 10/SPK/PU/DNP/XI/2017<br/>TANGGAL : 27 November 2018'.
												'</td>'.
											'</tr>'.
											'<tr style="page-break-before: always;">'.
												'<td style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">'.
													'<p id="page" class="nomargin"></p>'.
												'</td>'.
												'<td></td>'.
											'</tr>'.
											'<tr style="page-break-before: always;">'.
												'<td style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">'.
													'PAKET PEKERJAAN : PENGEMBANGAN WEBSITE AIM INDONESIA'.
												'</td>'.
												'<td style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">'.
													'NOMOR DAN TANGGAL DOKUMEN PENGADAAN : 10/DP/PU/DNP/XI/2017 27 Februari 2018'.
												'</td>'.
											'</tr>'.
											'<tr style="page-break-before: always;">'.
												'<td colspan="2" style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">'.
													'SUMBER DANA : Dibebankan atas PNBP Direktorat Navigasi Penerbangan Tahun Anggaran 2017 untuk mata anggaran kegiatan Belanja Modal Peralatan dan Mesin'.
												'</td>'.
											'</tr>'.
											'<tr style="page-break-before: always;">'.
												'<td colspan="2" style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">'.
													'TANGGAL MULAI : 27 November 2018'.
												'</td>'.
											'</tr>'.
											'<tr style="page-break-before: always;">'.
												'<td colspan="2" style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">'.
													'WAKTU PELAKSANAAN PEKERJAAN : 30 (Tiga puluh) hari kalender dan pekerjaan harus sudah selesai pada tanggal 26 Desember 2018'.
												'</td>'.
											'</tr>'.
											'<tr style="page-break-before: always;">'.
												'<td colspan="2" align="center" style="font-size: 12px !important;padding: 5px !important;border: 1px solid !important;">'.
													'NILAI PEKERJAAN'.
												'</td>'.
											'</tr>'.
											'<tr style="page-break-before: always;">'.
												'<td colspan="2" rowspan="" align="center"></td>'.
											'</tr>'.
										'</table>'.
										'<table style="margin-top: -4px !important;font-size: 12px !important;width:100%;border-spacing:0px;border: 1px #000 solid;margin-bottom: 30px !important;">'.
										'<tr style="page-break-before: always;">'.
											'<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;">No</td>'.
											'<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;">Uraian Kegiatan</td>'.
											'<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;">Volume</td>'.
											'<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;">Satuan</td>'.
											'<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;" colspan="2">Harga Satuan</td>'.
											'<td align="center" style="background-color:#CCC !important;font-size:12px;padding-top:3px;padding-bottom:3px;-webkit-print-color-adjust: exact;color-adjust: exact;border-color: black;" colspan="2">Harga Total</td>'.
										'</tr>'.
										'<tr style="page-break-before: always;">'.
											'<td style="padding: 5px !important;border: 1px solid !important;">0</td>'.
											'<td style="padding: 5px !important;border: 1px solid !important;">Penyedia hardware</td>'.
											'<td style="padding: 5px !important;border: 1px solid !important;">5</td>'.
											'<td style="padding: 5px !important;border: 1px solid !important;">paket</td>'.
											'<td style="padding: 5px !important;border: 1px solid !important;">RP</td>'.
											'<td style="padding: 5px !important;border: 1px solid !important;">1000000</td>'.
											'<td style="padding: 5px !important;border: 1px solid !important;">RP</td>'.
											'<td style="padding: 5px !important;border: 1px solid !important;">5000000</td>'.
										'</tr>'.
										'<tr style="page-break-before: always;">'.
											'<td style="padding: 5px !important;border: 1px solid !important;">1</td>'.
											'<td style="padding: 5px !important;border: 1px solid !important;">Instalasi os</td>'.
											'<td style="padding: 5px !important;border: 1px solid !important;">1</td>'.
											'<td style="padding: 5px !important;border: 1px solid !important;">license</td>'.
											'<td colspan="4" style="padding: 5px !important;border: 1px solid !important;text-align: center !important;">Include dalam pengadaan modul</td>'.
										'</tr>'.
									'</table>'.
									'<span style="margin-top:15px;">'.
										'<b>INSTRUKSI KEPADA PENYEDIA</b> : Penagihan hanya dapat dilakukan setelah penyelesaian pekerjaan yang diperintahkan dalam SPK ini dan dibuktikan dengan Berita Acara Serah Terima.'.
									'</span>'.
									'<table style="margin-top: 50px !important;width:100%;font-size: 12px !important;">'.
										'<tr style="page-break-before: always;">'.
											'<td align="center" style="border: 0px !important;">'.
												'<p>Untuk Dan Atas Nama</p>'.
												'<p><b>DIREKTORAT NAVIGASI PENERBANGAN</b></p>'.
												'<p><b>Pejabat Pembuat Komitmen</b></p>'.
											'</td>'.
											'<td align="center" style="border: 0px !important;">'.
												'<p>Untuk dan atas nama Penyedia Barang/Jasa ,</p>'.
												'<p><b>PT. ELECTRONIC DATA INTERCHANGE INDONESIA</b></p>'.
											'</td>'.
										'</tr>'.
										'<tr style="page-break-before: always;">'.
											'<td align="center" height="200" style="border: 0px !important;">'.
												'<p style="margin-bottom:-14px;"> <b>BAYU SEKTI AJI</b> </p>'.
												'<p>.............................................</p>'.
												'<p><b>Penata Tk.I (III/d)</b></p>'.
												'<p><b>NIP. 19861021 200812 1 002</b></p>'.
											'</td>'.
											'<td align="center" height="200" style="border: 0px !important;">'.
												'<p style="margin-bottom:-14px;"> <b>E. HELMI WANTONO</b> </p>'.
												'<p>..................................................</p>'.
												'<p><b>Direktur Utama</b></p>'.
											'</td>'.
										'</tr>'.
									'</table>'.
								'</div>'.
							'</div>';
							print_r($html3);die();
		\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html3,false,false);

		$objectWriter = IOFactory::createWriter($phpWord, 'Word2007');
	  try {
	      $objectWriter->save(storage_path('TestWordFile.docx'));
	  } catch (Exception $e) {
			return $e->getMessage();
	  }
		// return 'ssdd';
	  return response()->download(storage_path('TestWordFile.docx'));
	}

}
