
<div class="app-content main-content">
	<div class="side-app">
		<!--app header-->
		<div class="app-header header">
			<div class="container-fluid">
				<div class="d-flex">
					<div class="app-sidebar__toggle" data-toggle="sidebar">
						<a class="open-toggle" href="index.html#">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-align-left header-icon mt-1"><line x1="17" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="17" y1="18" x2="3" y2="18"></line></svg>
						</a>
					</div>
					<a class="header-brand" href="<?php echo URL ?>/index.php">
						<img src="<?php echo URL; ?>/app/assets/images/brand/logo.png" class="header-brand-img desktop-lgo">
						<img src="<?php echo URL; ?>/app/assets/images/brand/logo1.png" class="header-brand-img dark-logo">
						<img src="<?php echo URL; ?>/app/assets/images/brand/favicon.png" class="header-brand-img mobile-logo">
						<img src="<?php echo URL; ?>/app/assets/images/brand/favicon1.png" class="header-brand-img darkmobile-logo">
					</a>
					<div class="d-flex order-lg-2 ml-auto">
						<a href="<?php echo URL ?>/index.php" data-toggle="search" class="nav-link nav-link-lg d-md-none navsearch">
							<svg class="header-icon search-icon" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
								<path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
							</svg>
						</a>
						<div class="dropdown   header-fullscreen" >
							<a  class="nav-link icon full-screen-link p-0"  id="fullscreen-button">
								<svg xmlns="http://www.w3.org/2000/svg" class="header-icon" width="24" height="24" viewBox="0 0 24 24"><path d="M10 4L8 4 8 8 4 8 4 10 10 10zM8 20L10 20 10 14 4 14 4 16 8 16zM20 14L14 14 14 20 16 20 16 16 20 16zM20 8L16 8 16 4 14 4 14 10 20 10z"/></svg>
							</a>
						</div>

						<div class="dropdown header-notify">
							<a class="nav-link icon" data-toggle="dropdown">
								<svg xmlns="http://www.w3.org/2000/svg" class="header-icon" width="24" height="24" viewBox="0 0 24 24"><path d="M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3zm-4 9c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-8.9-5h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4l-3.87 7H8.53L4.27 2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2z"></path></svg>
								<span class="pulse "></span>
							</a>
							<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow  animated">
								<div class="dropdown-header">
									<h6 class="mb-0">Sepet</h6>
								</div>
								<div class="notify-menu"  id="cart-content">
									<?php
									require_once '../Controller/urunController.php';
									require_once '../Model/urunModel.php';
									$urunController = new urunController();
									if (isset($_SESSION['sepet']) && !empty($_SESSION['sepet'])) {
										foreach ($_SESSION['sepet'] as $urunId => $miktar) {
											$urunBilgileri = $urunController->detay($urunId);
											if ($urunBilgileri) {
												$foto = $urunBilgileri['foto'];
												$urunAdi = $urunBilgileri['isim'];
												$urunGram = $urunBilgileri['gram'];
												?>
												<a href="index.html#" class="dropdown-item border-bottom d-flex pl-4 sepet-urun" data-urun-id="<?php echo $urunId; ?>">
													<div class="notifyimg bg-primary-transparent text-primary"> <img src="<?php echo URL; ?>/app/assets/images/products/<?php echo $foto ?>" alt="img" class="avatar avatar-md brround"></i> </div>
													<div>
														<div class="font-weight-normal1"><?php echo $urunAdi . ' (' . $urunGram . ' gr)'; ?></div>
														<div class="small text-muted"><?php echo $miktar; ?> Adet</div>
													</div>
												</a>
												<?php
											}
										}
									} else {
										echo '<div class="dropdown-item text-center">Sepetinizde ürün bulunmamaktadır.</div>';
									}
									?>
								</div>
								<script type="text/javascript">
									document.querySelectorAll('.btn-secondary').forEach(function(button) {
										button.addEventListener('click', function(event) {
											event.preventDefault(); 	
											var urunId = this.form.querySelector('input[name="urun_id"]').value;
											var miktar = this.form.querySelector('input[name="miktar"]').value;
											var xhr = new XMLHttpRequest();
											xhr.open('POST', 'sepet.php');
											xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
											xhr.onload = function() {
												if (xhr.status === 200) {
													var sepetIcerigi = JSON.parse(xhr.responseText);
													updateSidebarSepet(sepetIcerigi);
													showSepetEklemeBildirimi(urunId, miktar);
												} else {
													console.error('Hata:', xhr.statusText);
												}
											};
											xhr.send('urun_id=' + urunId + '&miktar=' + miktar);
										});
									});
									function updateSidebarSepet(sepetIcerigi) {
										document.getElementById('cart-content').innerHTML = '';
										for (var urunId in sepetIcerigi) {
											var urun = sepetIcerigi[urunId];
											var html = `
											<a href="#" class="dropdown-item border-bottom d-flex pl-4 sepet-urun" data-urun-id="${urunId}">
											<div class="notifyimg bg-primary-transparent text-primary"> <img src="<?php echo URL; ?>/app/assets/images/products/${urun.foto}" alt="img" class="avatar avatar-md brround"></i> </div>
											<div>
											<div class="font-weight-normal1">${urun.isim} (${urun.gram} gr)</div>
											<div class="small text-muted">${urun.miktar} Adet</div>
											</div>
											</a>
											`;
											document.getElementById('cart-content').innerHTML += html;
										}
									}
									function showSepetEklemeBildirimi(urunId, miktar) {
										var bildirim = document.createElement('div');
										bildirim.classList.add('sepet-bildirim');
										bildirim.textContent = `${miktar} adet "${urunBilgileri['isim']}" ürün sepete eklendi.`;
										document.body.appendChild(bildirim);

										setTimeout(function() {
											bildirim.style.opacity = '1';
										}, 100);

										setTimeout(function() {
											bildirim.style.opacity = '0';
											setTimeout(function() {
												bildirim.remove();
											}, 500);
										}, 5000);
									}
								</script>



								<div class=" text-center p-2 border-top">
									<a href="<?php echo URL ?>/public/Views/sepet.php" class="">Alışverişi Tamamla</a>
								</div>

							</div>
						</div>
						<div class="dropdown profile-dropdown">
							<a class="nav-link icon" data-toggle="dropdown">
								<svg xmlns="http://www.w3.org/2000/svg" class="header-icon" width="24" height="24" viewBox="0 0 24 24">
									<path d="M12 2C9.24 2 7 4.24 7 7c0 1.86.78 3.52 2.03 4.72C7.27 12.92 2 14.76 2 17v3h20v-3c0-2.24-5.27-4.08-7.03-5.28C16.22 10.52 17 8.86 17 7c0-2.76-2.24-5-5-5zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm9 9h-2v-3h-3v-2h3v-3h2v3h3v2h-3v3z"/>
								</svg>
							</a>
							<?php 
							if ($_SESSION['firma']) {
								$isim=$_SESSION['firma'];
							}
							if ($_SESSION['rol']) {
								$rol=$_SESSION['rol'];
							}
							?>
							<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow animated">
								<div class="text-center">
									<a class="dropdown-item text-center user pb-0 font-weight-bold"><?php echo $isim; ?></a>
									<span class="text-center user-semi-title"><?php echo $rol ?></span>
									<div class="dropdown-divider"></div>
								</div>
								<a class="dropdown-item d-flex" href="../Controller/girisController.php?cikisYap=true">
									<svg class="header-icon mr-3" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24" viewBox="0 0 24 24" width="24">
										<g><rect fill="none" height="24" width="24"/></g>
										<g><path d="M11,7L9.6,8.4l2.6,2.6H2v2h10.2l-2.6,2.6L11,17l5-5L11,7z M20,19h-8v2h8c1.1,0,2-0.9,2-2V5c0-1.1-0.9-2-2-2h-8v2h8V19z"/></g>
									</svg>
									<div class="">Çıkış Yap</div>
								</a>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>