<?php
	require 'config.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<title>upload</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> 
		<link rel="stylesheet" href="assets/style.css">
	</head>
	<body>
		<div class="container mt-5 ">
			<div class="card">
				<div class="card-header bg-dark text-white">
					โอนเงินมาที่
				</div>
				<div class="card-body">
					<div class="scb-box d-flex flex-row align-items-center justify-content-start" style="background: #4e2e7f;">
						<img src="https://pbs.twimg.com/profile_images/924662185929752576/9cRHWYxV_400x400.jpg" class="img-icon rounded-circle img-fluid" width="75" hight="75" alt="">
						<div class="flex-grow-1" style="padding-left:8px;text-align: right;">
							<div id="account" style="font-size: 24px; font-weight: bold; margin: -5px 0; letter-spacing: 3px;"><font color="white"><?=$config['acc_num']?></font></div>
							<div class="font14 mt-2">ธนาคารไทยพาณิชย์</div>
							<div class="font14"><?=$config['acc_name']?></div>
							<span class="btn btn-danger btn-scb btn-sm justify-content-end copy-data"   onclick="copyToClipboard('#account')"><i class="fal fa-copy"></i> คัดลอกเลขบัญชี</span>
						</div>
					</div>
				</div>
			</div>
			<div class="row ">
				<div class="col-md-4 mt-2">
					<div class="card">
						<div class="card-header bg-dark text-white">
							อัพโหลดสลิป
						</div>
						<div class="card-body">
							<form action="upload.php" method="POST" enctype="multipart/form-data" id="upload">
								<div class="form-group">
									<div class="input-group">
										<div class="custom-file">
											<label class="custom-file-label" for="image">Choose file</label>
											<input type="file" class="custom-file-input" id="image" name="image" aria-describedby="inputGroupFileAddon04" required>
										</div>
									</div>
								</div>
								<center><button type="submit" class="btn btn-success">อัพโหลดสลิป</button></center>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-8  mt-2">
					<div class="card ">
					<div class="card-header bg-dark text-white">รายการล่าสุด</div>
						<div class="table-responsive">
							<table class="table table-striped text-center">
								<thead>
									<tr>
										<th scope="col">transRef</th>
										<th scope="col">amount</th>
										<th scope="col">date</th>
									</tr>
								</thead>
								<tbody id="result">
								</tbody>
							</table>
						</div>
					</div>  
				</div>
			</div>
		</div>
		<script src="main.js"></script>
	</body>
</html>