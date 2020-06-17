@extends('donator::mail-template')

@section('content')
<!-- Start of main-banner -->
<table width="100%" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="banner">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="600" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth">
                           <tbody>
                              <tr>
                                 <!-- start of image -->
                                 <td align="center" st-image="banner-image">
                                    <div class="imgpop">
                                       <a target="_blank" href="#"><img width="156" border="0" height="132" alt="" border="0" style="display:block; border:none; outline:none; text-decoration:none;" src="img/terima-kasih.png" class="banner"></a>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                        <!-- end of image -->
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of main-banner --> 
<!-- Start of seperator -->
<table width="100%" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="seperator">
   <tbody>
      <tr>
         <td>
            <table width="600" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth">
               <tbody>
                  <tr>
                     <td align="center" height="20" style="font-size:1px; line-height:1px;">&nbsp;</td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of seperator -->   
<!-- Start Full Text -->
<table width="100%" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="full-text">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                       <tbody>
                                          <!-- Title -->
                                          <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; font-size: 22px; color: #ED1C24; text-align:center; line-height: 30px;" st-title="fulltext-heading">
                                                Hi {{ (isset($donation))? $donation->name : ''  }}
                                             </td>
                                          </tr>
                                          <!-- End of Title -->
                                          <!-- spacing -->
                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- content -->
                                          <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; font-size: 14px; color: #3E3E3E; text-align:center; line-height: 30px;" st-content="fulltext-content">
                                                Terima kasih sudah melakukan donasi.<br>Rincian donasi kamu adalah sebagai berikut:
                                             </td>
                                          </tr>
                                          <!-- End of content -->
                                          <!-- spacing -->
                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- invoice -->
                                          <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; text-align:left; line-height: 30px;" st-content="fulltext-content">
                                                <label style="font-size: 12px; font-weight: bold; color: #3E3E3E;">Judul:</label>
                                                <p style="font-size: 12px; color: #3E3E3E; line-height: 20px;"> {{ (isset($donation->campaign->title))? $donation->campaign->title : ''  }}</p>
                                             </td>
                                          </tr>
                                          <!-- spacing -->
                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; text-align:left; line-height: 30px;" st-content="fulltext-content">
                                                <label style="font-size: 12px; font-weight: bold; color: #3E3E3E;">Metode Transfer:</label>
                                                <p style="font-size: 12px; color: #3E3E3E; line-height: 20px;"> {{ (isset($donation->payment_method_text))? $donation->payment_method_text : ''  }}</p>
                                             </td>
                                          </tr>
                                          <!-- spacing -->
                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; text-align:left; line-height: 30px;" st-content="fulltext-content">
                                                <label style="font-size: 12px; font-weight: bold; color: #3E3E3E;">Jumlah Donasi:</label>
                                                <p style="font-size: 12px; color: #ED1C24; line-height: 20px; font-weight: bold">{{ (isset($donation->amount))? $donation->amount : ''  }}</p>
                                             </td>
                                          </tr>
                                          <!-- End of invoice -->
                                          <!-- spacing -->
                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- content -->
                                          <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; font-size: 14px; color: #3E3E3E; text-align:center; line-height: 30px;" st-content="fulltext-content">
                                                Silahkan mentransfer Donasi Kamu ke:
                                             </td>
                                          </tr>
                                          <!-- End of content -->
                                          <!-- spacing -->
                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- bank -->
                                             <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; line-height: 30px; background-color: #f3f3f3; text-align: center;" st-content="fulltext-content">
                                                <h2 style="font-size: 14px; color: #3E3E3E; line-height: 20px; font-weight: bold">Bank Mandiri</h2>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; line-height: 30px; background-color: #f3f3f3; text-align: center;" st-content="fulltext-content">
                                                <label style="font-size: 9px; color: #3E3E3E;">Nomer Rekening:</label>
                                                <p style="font-size: 14px; color: #3E3E3E; line-height: 20px; font-weight: bold">123 001709 1945</p>
                                             </td>
                                          </tr>
                                          <tr>
                                          <td style="font-family: 'Open Sans', sans-serif; line-height: 30px; background-color: #f3f3f3; padding-bottom: 15px; text-align: center;" st-content="fulltext-content">
                                                <label style="font-size: 9px; color: #3E3E3E;">Atas Nama:</label>
                                                <p style="font-size: 14px; color: #3E3E3E; line-height: 20px;">PMI DKI JAKARTA</p>
                                             </td>
                                          </tr>
                                          <!-- End of bank -->
                                          <!-- spacing -->
                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- bank -->
                                             <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; line-height: 30px; background-color: #f3f3f3;text-align: center;" st-content="fulltext-content">
                                                <h2 style="font-size: 14px; color: #3E3E3E; line-height: 20px; font-weight: bold">BCA</h2>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; line-height: 30px; background-color: #f3f3f3; text-align: center;" st-content="fulltext-content">
                                                <label style="font-size: 9px; color: #3E3E3E;">Nomer Rekening:</label>
                                                <p style="font-size: 14px; color: #3E3E3E; line-height: 20px; font-weight: bold">2063 8179 45</p>
                                             </td>
                                          </tr>
                                          <tr>
                                          <td style="font-family: 'Open Sans', sans-serif; line-height: 30px; background-color: #f3f3f3; padding-bottom: 15px; text-align: center;" st-content="fulltext-content">
                                                <label style="font-size: 9px; color: #3E3E3E;">Atas Nama:</label>
                                                <p style="font-size: 14px; color: #3E3E3E; line-height: 20px;">PMI DKI JAKARTA</p>
                                             </td>
                                          </tr>
                                          <!-- End of bank -->
                                          <!-- spacing -->
                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- bank -->
                                             <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; line-height: 30px; background-color: #f3f3f3; text-align: center;" st-content="fulltext-content">
                                                <h2 style="font-size: 14px; color: #3E3E3E; line-height: 20px; font-weight: bold">CIMB NIAGA</h2>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="font-family: 'Open Sans', sans-serif; line-height: 30px; background-color: #f3f3f3; text-align: center;" st-content="fulltext-content">
                                                <label style="font-size: 9px; color: #3E3E3E;">Nomer Rekening:</label>
                                                <p style="font-size: 14px; color: #3E3E3E; line-height: 20px; font-weight: bold">800069514600</p>
                                             </td>
                                          </tr>
                                          <tr>
                                          <td style="font-family: 'Open Sans', sans-serif; line-height: 30px; background-color: #f3f3f3; padding-bottom: 15px; text-align: center;" st-content="fulltext-content">
                                                <label style="font-size: 9px; color: #3E3E3E;">Atas Nama:</label>
                                                <p style="font-size: 14px; color: #3E3E3E; line-height: 20px;">PMI DKI JAKARTA</p>
                                             </td>
                                          </tr>
                                          <!-- End of bank -->
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- end of full text -->
@endsection