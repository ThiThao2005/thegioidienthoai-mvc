<div class="container-fluid px-4 py-4">
    <div class="row g-4">
        <div class="col-lg-3 col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex flex-column gap-2">
                    <a href="/project1/Product/dashboard" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-chart-pie me-2 text-warning"></i>Tổng quan hệ thống
                    </a>
                    <a href="/project1/Product/index" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-box me-2 text-primary"></i>Quản lý sản phẩm
                    </a>
                    <a href="/project1/Product/categories" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-tags me-2 text-success"></i>Quản lý danh mục
                    </a>
                    <a href="/project1/Product/orders" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-shopping-bag me-2 text-danger"></i>Quản lý đơn hàng
                    </a>
                    <a href="/project1/Product/users" class="nav-link p-3 bg-light text-dark fw-bold active border-start border-warning border-3">
                        <i class="fas fa-users me-2 text-info"></i>Quản lý người dùng
                    </a>
                    <hr class="my-2 opacity-50">
                  <a href="/project1/Product/dashboard" class="nav-link p-3 text-dark fw-semibold">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại trang chủ
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-8">
            <h3 class="fw-bold text-dark text-uppercase mb-4">
                <i class="fas fa-users text-warning me-2"></i>Quản lý tài khoản người dùng
            </h3>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                <div class="table-responsive bg-white rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr class="small text-uppercase">
                                <th class="text-center py-3" style="width: 80px;">ID</th>
                                <th class="py-3">Họ và Tên</th>
                                <th class="py-3">Loại tài khoản</th>
                                <th class="text-center py-3" style="width: 200px;">Vai trò (Click để đổi)</th>
                                <th class="text-center py-3" style="width: 150px;">Thao tác</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users-slash fa-3x mb-3 text-opacity-25 text-secondary"></i>
                                            <p class="mb-0 fw-semibold text-secondary">Hệ thống chưa có thành viên nào đăng ký.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                <tr style="transition: all 0.2s ease; border-bottom: 1px solid #f1f1f1;">
                                    <td class="text-center py-3">
                                        <span class="badge bg-light text-dark border px-2 py-1 d-inline-block fw-bold" style="min-width: 45px;">
                                            #<?= $u->id ?>
                                        </span>
                                    </td>
                                    
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <?php 
                                                $bg_gradients = [
                                                    'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
                                                    'linear-gradient(135deg, #f6d365 0%, #fda085 100%)',
                                                    'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)',
                                                    'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)'
                                                ];
                                                $gradient = $bg_gradients[$u->id % 4];
                                                $first_letter = strtoupper(substr($u->fullname ?? $u->username ?? 'U', 0, 1));
                                            ?>
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" 
                                                 style="width: 42px; height: 42px; background: <?= $gradient ?>; font-size: 1.1rem;">
                                                <?= $first_letter ?>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block mb-0 fs-6"><?= htmlspecialchars($u->fullname, ENT_QUOTES, 'UTF-8') ?></span>
                                                <span class="text-muted small d-inline-flex align-items-center gap-1">
                                                    <i class="far fa-user-circle small text-opacity-50"></i>@<?= htmlspecialchars($u->username, ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="py-3">
                                        <span class="text-dark fw-medium small d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-laptop-code text-warning small"></i> Tài khoản hệ thống
                                        </span>
                                    </td>
                                    
                                    <td class="text-center py-3">
                                        <form action="/project1/Product/changeRole" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi quyền của tài khoản này không?');">
                                            <input type="hidden" name="user_id" value="<?= $u->id ?>">
                                            
                                            <?php if (strtolower($u->role) === 'admin'): ?>
                                                <input type="hidden" name="role" value="user">
                                                <button type="submit" class="btn p-0 border-0 bg-transparent">
                                                    <span class="badge rounded-pill fw-bold px-3 py-2 shadow-sm" 
                                                          style="background-color: #fff1f0; color: #cf1322; border: 1px solid #ffa39e; font-size: 0.78rem;">
                                                        <i class="fas fa-shield-alt me-1"></i> Quản trị viên
                                                    </span>
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="role" value="admin">
                                                <button type="submit" class="btn p-0 border-0 bg-transparent">
                                                    <span class="badge rounded-pill fw-bold px-3 py-2 shadow-sm" 
                                                          style="background-color: #e6f7ff; color: #096dd9; border: 1px solid #91d5ff; font-size: 0.78rem;">
                                                        <i class="fas fa-shopping-bag me-1"></i> Khách hàng
                                                    </span>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                    
                                    <td class="text-center py-3">
                                        <a href="/project1/Product/deleteUser?id=<?= $u->id ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5"
                                           style="font-size: 0.82rem; transition: all 0.2s;"
                                           onclick="return confirm('Bạn có chắc chắn muốn gỡ tài khoản này khỏi hệ thống không? Hành động này không thể hoàn tác!');">
                                            <i class="fas fa-user-minus me-1"></i> Xóa tài khoản
                                        </a>
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