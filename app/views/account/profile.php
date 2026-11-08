<?php require_once 'app/views/layout/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="card-body">

                    <div class="text-center mb-4">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?= htmlspecialchars('/public/uploads/avatars/' . $user['avatar']) ?>"
                                 class="rounded-circle shadow-sm mb-3"
                                 style="width:110px;height:110px;object-fit:cover;" alt="Ảnh đại diện">
                        <?php else: ?>
                            <?php $profileInitial = mb_strtoupper(mb_substr($user['fullname'] ?? 'U', 0, 1, 'UTF-8'), 'UTF-8'); ?>
                            <div class="rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center"
                                 style="width:110px;height:110px;background:var(--brand);color:var(--ink);font-size:2.6rem;font-weight:700;box-shadow:0 0 0 6px var(--brand-soft);">
                                <?= htmlspecialchars($profileInitial) ?>
                            </div>
                        <?php endif; ?>

                        <h3 class="fw-bold">HỒ SƠ CÁ NHÂN</h3>
                        <p class="text-secondary small">Xem và cập nhật thông tin tài khoản</p>
                    </div>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="/Account/updateProfile" method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ảnh đại diện</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên đăng nhập</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Họ và tên</label>
                            <input type="text" name="fullname" class="form-control" required value="<?= htmlspecialchars($user['fullname'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Địa chỉ</label>
                            <textarea name="address" rows="3" class="form-control"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning fw-bold w-50">
                                Cập nhật hồ sơ
                            </button>
                            <a href="/Account/changePassword" class="btn btn-dark fw-bold w-50">
                                Đổi mật khẩu
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'app/views/layout/footer.php'; ?>