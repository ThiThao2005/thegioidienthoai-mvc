<?php require_once 'app/views/layout/header.php'; ?>

<div class="container-fluid px-4 py-4">
    <div class="row g-4">

        <div class="col-lg-3 col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex flex-column gap-2">
                    <a href="/Product/dashboard" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-chart-pie me-2 text-warning"></i> Tổng quan hệ thống
                    </a>

                    <a href="/Product/index" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-box me-2 text-primary"></i> Quản lý sản phẩm
                    </a>

                    <a href="/Product/categories" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-tags me-2 text-success"></i> Quản lý danh mục
                    </a>

                    <a href="/Product/orders" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-shopping-bag me-2 text-danger"></i> Quản lý đơn hàng
                    </a>

                    <a href="/Account/users" class="nav-link p-3 bg-light text-dark fw-bold active border-start border-warning border-3">
                        <i class="fas fa-users me-2 text-info"></i> Quản lý người dùng
                    </a>

                    <hr class="my-2 opacity-50">

                    <a href="/Product/dashboard" class="nav-link p-3 text-dark fw-semibold">
                        <i class="fas fa-arrow-left me-2"></i> Quay lại trang chủ
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-8">
            <h3 class="fw-bold text-dark text-uppercase mb-4">
                <i class="fas fa-users text-warning me-2"></i>
                Quản lý tài khoản người dùng
            </h3>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                <div class="table-responsive bg-white rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr class="small text-uppercase">
                                <th class="text-center py-3" style="width: 80px;">ID</th>
                                <th class="py-3" style="min-width: 240px;">Người dùng</th>
                                <th class="py-3" style="min-width: 180px;">Email</th>
                                <th class="py-3" style="min-width: 150px;">Số điện thoại</th>
                                <th class="py-3" style="min-width: 220px;">Địa chỉ</th>
                                <th class="text-center py-3" style="width: 120px;">Vai trò</th>
                                <th class="text-center py-3" style="width: 140px;">Xác thực</th>
                                <th class="text-center py-3" style="width: 140px;">Trạng thái</th>
                                <th class="text-center py-3" style="width: 260px;">Thao tác</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-3 text-opacity-25 text-secondary"></i>
                                        <p class="mb-0 fw-semibold text-secondary">
                                            Hệ thống chưa có thành viên nào đăng ký.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>

                            <?php foreach ($users as $u): ?>
                                <?php
                                    $id = $u->id ?? '';
                                    $username = $u->username ?? '';
                                    $fullname = $u->fullname ?? $username;
                                    $email = $u->email ?? '';
                                    $phone = $u->phone ?? '';
                                    $address = $u->address ?? '';
                                    $avatar = $u->avatar ?? '';
                                    $role = $u->role ?? 'user';
                                    $status = $u->status ?? 'active';
                                    $emailVerified = $u->email_verified ?? 0;

                                    $avatarPath = !empty($avatar)
                                        ? '/public/uploads/avatars/' . $avatar
                                        : '';

                                    $firstLetter = mb_strtoupper(
                                        mb_substr($fullname ?: $username ?: 'U', 0, 1, 'UTF-8'),
                                        'UTF-8'
                                    );

                                    $bgGradients = [
                                        'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
                                        'linear-gradient(135deg, #f6d365 0%, #fda085 100%)',
                                        'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)',
                                        'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)'
                                    ];

                                    $gradient = $bgGradients[((int)$id) % 4];
                                ?>

                                <tr style="transition: all 0.2s ease; border-bottom: 1px solid #f1f1f1;">

                                    <td class="text-center py-3">
                                        <span class="badge bg-light text-dark border px-2 py-1 d-inline-block fw-bold" style="min-width: 45px;">
                                            #<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>

                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($avatarPath)): ?>
                                                <img src="<?= htmlspecialchars($avatarPath, ENT_QUOTES, 'UTF-8') ?>?v=<?= time() ?>"
                                                     alt="Avatar"
                                                     class="rounded-circle shadow-sm border"
                                                     style="width: 48px; height: 48px; object-fit: cover;"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                                <div class="rounded-circle align-items-center justify-content-center fw-bold text-white shadow-sm border"
                                                     style="width: 48px; height: 48px; background: <?= $gradient ?>; font-size: 1.1rem; display:none;">
                                                    <?= htmlspecialchars((string)$firstLetter, ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm border"
                                                     style="width: 48px; height: 48px; background: <?= $gradient ?>; font-size: 1.1rem;">
                                                    <?= htmlspecialchars((string)$firstLetter, ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                            <?php endif; ?>

                                            <div>
                                                <span class="fw-bold text-dark d-block mb-0 fs-6">
                                                    <?= htmlspecialchars((string)$fullname, ENT_QUOTES, 'UTF-8') ?>
                                                </span>

                                                <span class="text-muted small">
                                                    Tài khoản ID #<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3">
                                        <?php if (!empty($email)): ?>
                                            <span class="text-dark small">
                                                <i class="fas fa-envelope text-warning me-1"></i>
                                                <?= htmlspecialchars((string)$email, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small fst-italic">Chưa cập nhật</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="py-3">
                                        <?php if (!empty($phone)): ?>
                                            <span class="text-dark small">
                                                <i class="fas fa-phone text-success me-1"></i>
                                                <?= htmlspecialchars((string)$phone, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small fst-italic">Chưa cập nhật</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="py-3">
                                        <?php if (!empty($address)): ?>
                                            <span class="text-dark small">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                <?= htmlspecialchars((string)$address, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small fst-italic">Chưa cập nhật</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center py-3">
                                        <form action="/Account/changeRole" method="POST"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi quyền của tài khoản này không?');">
                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>">

                                            <?php if (strtolower((string)$role) === 'admin'): ?>
                                                <input type="hidden" name="role" value="user">
                                                <button type="submit" class="btn p-0 border-0 bg-transparent">
                                                    <span class="badge rounded-pill fw-bold px-3 py-2 shadow-sm"
                                                          style="background-color: #fff1f0; color: #cf1322; border: 1px solid #ffa39e; font-size: 0.78rem;">
                                                        <i class="fas fa-shield-alt me-1"></i> Admin
                                                    </span>
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="role" value="admin">
                                                <button type="submit" class="btn p-0 border-0 bg-transparent">
                                                    <span class="badge rounded-pill fw-bold px-3 py-2 shadow-sm"
                                                          style="background-color: #e6f7ff; color: #096dd9; border: 1px solid #91d5ff; font-size: 0.78rem;">
                                                        <i class="fas fa-user me-1"></i> User
                                                    </span>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>

                                    <td class="text-center py-3">
                                        <?php if ((int)$emailVerified === 1): ?>
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i> Đã xác thực
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                <i class="fas fa-clock me-1"></i> Chưa xác thực
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center py-3">
                                        <?php if ((string)$status === 'locked'): ?>
                                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                                <i class="fas fa-lock me-1"></i> Đã khóa
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="fas fa-unlock me-1"></i> Hoạt động
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center py-3">
                                        <div class="d-flex flex-wrap justify-content-center gap-2">
                                            <?php if ((string)$status === 'locked'): ?>
                                                <a href="/Account/unlockUser?id=<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>"
                                                   class="btn btn-sm btn-outline-success rounded-pill px-3"
                                                   onclick="return confirm('Mở khóa tài khoản này?');">
                                                    <i class="fas fa-unlock me-1"></i> Mở khóa
                                                </a>
                                            <?php else: ?>
                                                <a href="/Account/lockUser?id=<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>"
                                                   class="btn btn-sm btn-outline-warning rounded-pill px-3"
                                                   onclick="return confirm('Khóa tài khoản này?');">
                                                    <i class="fas fa-lock me-1"></i> Khóa
                                                </a>
                                            <?php endif; ?>

                                            <a href="/Account/deleteUser?id=<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>"
                                               class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không? Hành động này không thể hoàn tác!');">
                                                <i class="fas fa-user-minus me-1"></i> Xóa
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once 'app/views/layout/footer.php'; ?>