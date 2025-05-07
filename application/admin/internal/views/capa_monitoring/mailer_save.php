<p style="text-align: justify;">Yth. Bapak/Ibu <?php echo $nama_user; ?> . <br><?php echo $description; ?>.</p>
<ul>
  <li><strong>Area Audit :</strong><?php echo $detail['audit_area'] ;?></li>
  <li><strong>Judul Temuan :</strong><?php echo $detail['finding'] ;?></li>
  <li><strong>Tanggal Update :</strong><?php echo date_indo($detail['tanggal']) ;?></li>
</ul>
<br>
<p style="text-align: justify;">Mohon untuk memverifikasi perkembangan pelaksanaan CAPA dan memberikan status/komentar sesuai kebijakan verifikasi internal audit.</p>
<p style="text-align: justify;">Akses sistem di:</p>
<p style="text-align: justify;">
	<a href="<?php echo $url; ?>" target="_blank" style="background: #16D39A; color: #fff; padding: .5rem 1rem; border-radius: .175rem; text-decoration: none;">Audit Management System</a>
</p>
<p style="text-align: justify;">Terima kasih atas kerja samanya.</p>
<br>
<br>
<p style="text-align: justify;">Salam,<br>
Audit Management System – Notifikasi Otomatis</p>