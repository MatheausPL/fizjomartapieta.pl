<?php
$log_file = "logs/changes.log";
$all_logs = [];

if (file_exists($log_file)) {
    $file_lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($file_lines !== false) {
        $all_logs = $file_lines;
    }
}

// 1. ODBIERANIE PARAMETRÓW FILTROWANIA, SORTOWANIA I STRONY Z URL
$filter_section = isset($_GET['f_section']) ? trim($_GET['f_section']) : '';
$filter_action  = isset($_GET['f_action']) ? trim($_GET['f_action']) : '';
$search_query   = isset($_GET['f_search']) ? trim($_GET['f_search']) : '';
$sort_order     = isset($_GET['f_sort']) ? trim($_GET['f_sort']) : 'desc';

// NOWOŚĆ: Logika ustalania aktualnej strony paginacji
$per_page = 20; // Definiujemy dokładnie 20 zmian na stronę
$current_page = isset($_GET['p']) ? intval($_GET['f_page'] ?? $_GET['p']) : 1;
if ($current_page < 1) { $current_page = 1; }

$sections_list = [];
$actions_list = [];
$filtered_logs = [];

// 2. PRZEBIEG 1: PARSOWANIE I FILTROWANIE DANYCH WEJŚCIOWYCH
foreach ($all_logs as $log_line) {
    $parts = explode(' | ', $log_line);
    if (count($parts) !== 7) { continue; }

    $log_sec = trim($parts[3]);
    $log_act = trim($parts[4]);

    if (!in_array($log_sec, $sections_list) && $log_sec !== '') { $sections_list[] = $log_sec; }
    if (!in_array($log_act, $actions_list) && $log_act !== '') { $actions_list[] = $log_act; }

    if ($filter_section !== '' && $log_sec !== $filter_section) { continue; }
    if ($filter_action !== '' && $log_act !== $filter_action) { continue; }
    
    if ($search_query !== '') {
        if (mb_stripos($log_line, $search_query) === false) { continue; }
    }

    $filtered_logs[] = $log_line;
}

// 3. LOGIKA SORTOWANIA W PHP
if ($sort_order === 'desc') {
    $filtered_logs = array_reverse($filtered_logs);
}

// 4. NOWOŚĆ: MATEMATYCZNE OBLICZANIE STRON (PAGINACJA)
$total_filtered_items = count($filtered_logs);
$total_pages = ceil($total_filtered_items / $per_page);
if ($total_pages < 1) { $total_pages = 1; }
if ($current_page > $total_pages) { $current_page = $total_pages; }

// Wyliczamy indeks startowy dla wycinania tablicy (np. dla strony 2 start to 20)
$start_index = ($current_page - 1) * $per_page;

// WYCINAMY WYŁĄCZNIE 20 ELEMENTÓW DLA AKTUALNEJ STRONY!
$paged_logs = array_slice($filtered_logs, $start_index, $per_page);

// Pomocnicza funkcja do budowania linków stron, zachowująca obecne filtry w URL
function buildPaginationUrl($page_num) {
    $params = $_GET;
    $params['p'] = $page_num; // nadpisujemy lub dodajemy numer strony
    return "admin.php?" . http_build_query($params);
}
?>

<style>
.admin-card { background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #e0e0e0; font-family: Arial, sans-serif; }
.admin-title { margin-top: 0; margin-bottom: 20px; color: #333333; font-size: 1.5rem; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; }
.filter-bar { background: #f8f9fa; padding: 16px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #e2e8f0; display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; }
.filter-group { display: flex; flex-direction: column; gap: 4px; }
.filter-group label { font-size: 0.82rem; font-weight: bold; color: #4a5568; }
.filter-control { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.9rem; background: #ffffff; min-width: 150px; box-sizing: border-box; }
.filter-control:focus { border-color: #222222; outline: none; }
.btn-filter-submit { background: #222222; color: white; border: none; padding: 9px 16px; border-radius: 4px; font-size: 0.9rem; font-weight: bold; cursor: pointer; height: 38px; text-decoration: none; display: inline-flex; align-items: center; }
.btn-filter-clear { background: #e2e8f0; color: #334155; border: none; padding: 9px 16px; border-radius: 4px; font-size: 0.9rem; font-weight: bold; cursor: pointer; height: 38px; text-decoration: none; display: inline-flex; align-items: center; }

.log-table-container { overflow-x: auto; }
.log-table { width: 100%; border-collapse: collapse; background: #ffffff; text-align: left; }
.log-table th { background: #f1f5f9; color: #475569; font-weight: bold; padding: 12px 14px; border-bottom: 2px solid #cbd5e1; font-size: 0.88rem; }
.log-table td { padding: 12px 14px; border-bottom: 1px solid #e2e8f0; color: #333333; font-size: 0.9rem; vertical-align: middle; }
.log-table tr:hover { background: #f8fafc; }
.log-timestamp { color: #475569; font-family: monospace; font-size: 0.85rem; white-space: nowrap; }
.log-user { font-weight: bold; color: #475569; }
.log-section { font-weight: bold; color: #1e293b; }

.log-badge { display: inline-block; padding: 4px 8px; font-size: 0.75rem; font-weight: bold; border-radius: 4px; text-align: center; min-width: 80px; }
.badge-blue { background: #e0f2fe; color: #0369a1; }
.badge-green { background: #e8f5e9; color: #2e7d32; }
.badge-red { background: #ffebee; color: #c62828; }
.badge-default { background: #f5f5f5; color: #666666; }
.badge-object { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

.btn-toggle-details { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; cursor: pointer; transition: background 0.2s; }
.btn-toggle-details:hover { background: #e2e8f0; }
.details-row { display: none; background: #f8fafc; }
.details-row.open { display: table-row; }
.details-cell { padding: 12px 20px !important; border-bottom: 1px solid #cbd5e1 !important; background: #fafafa; }

.history-item { margin-bottom: 8px; font-size: 0.88rem; color: #334155; display: flex; align-items: flex-start; gap: 8px; }
.history-item:last-child { margin-bottom: 0; }
.history-label { font-weight: bold; color: #475569; min-width: 140px; display: inline-block; text-align: right; }
.history-box { font-size: 0.85rem; color: #334155; background: #ffffff; padding: 6px 12px; border-radius: 4px; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; flex: 1; flex-wrap: wrap; }
.history-arrow { color: #64748b; font-weight: bold; margin: 0 10px; }

/* STYLE DLA STRONICOWANIA (PAGINACJI) */
.pagination-container { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 0.9rem; color: #4a5568; }
.pagination-links { display: inline-flex; gap: 6px; list-style: none; padding: 0; margin: 0; }
.pagination-link { display: inline-block; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 4px; text-decoration: none; color: #334155; font-weight: bold; transition: background 0.2s; }
.pagination-link:hover { background: #f1f5f9; }
.pagination-link.active { background: #222222; color: #ffffff; border-color: #222222; cursor: default; }
.pagination-link.disabled { color: #cbd5e1; border-color: #e2e8f0; cursor: not-allowed; pointer-events: none; }
</style>
<div class="admin-card">
    <h2 class="admin-title">Szczegółowa historia zmian systemowych</h2>
    
    <form method="GET" action="admin.php" class="filter-bar">
        <input type="hidden" name="page" value="logi">
        <!-- Resetujemy na stronę 1 przy ponownym zatwierdzeniu filtrów -->
        <input type="hidden" name="p" value="1"> 

        <div class="filter-group">
            <label for="f_section">Filtruj zakładkę:</label>
            <select id="f_section" name="f_section" class="filter-control">
                <option value="">-- Wszystkie --</option>
                <?php foreach ($sections_list as $sec): ?>
                    <option value="<?= htmlspecialchars($sec) ?>" <?= $filter_section === $sec ? 'selected' : '' ?>><?= htmlspecialchars($sec) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="f_action">Filtruj operację:</label>
            <select id="f_action" name="f_action" class="filter-control">
                <option value="">-- Wszystkie --</option>
                <?php foreach ($actions_list as $act): ?>
                    <option value="<?= htmlspecialchars($act) ?>" <?= $filter_action === $act ? 'selected' : '' ?>><?= htmlspecialchars($act) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="f_sort">Chronologia:</label>
            <select id="f_sort" name="f_sort" class="filter-control">
                <option value="desc" <?= $sort_order === 'desc' ? 'selected' : '' ?>>Od najnowszych</option>
                <option value="asc" <?= $sort_order === 'asc' ? 'selected' : '' ?>>Od najstarszych</option>
            </select>
        </div>

        <div class="filter-group" style="flex: 1; min-width: 200px;">
            <label for="f_search">Szukaj frazy:</label>
            <input type="text" id="f_search" name="f_search" class="filter-control" style="width:100%;" value="<?= htmlspecialchars($search_query) ?>" placeholder="Wpisz frazę do wyszukania...">
        </div>

        <div style="display:flex; gap:8px;">
            <button type="submit" class="btn-filter-submit">Filtruj</button>
            <a href="admin.php?page=logi" class="btn-filter-clear">Reset</a>
        </div>
    </form>
    
    <div class="log-table-container">
        <table class="log-table">
            <thead>
                <tr>
                    <th style="width: 140px;">Data i godzina</th>
                    <th style="width: 100px;">Użytkownik</th>
                    <th style="width: 100px;">Zakładka</th>
                    <th style="width: 110px;">Operacja</th>
                    <th style="width: 150px;">Element</th>
                    <th style="width: 100px; text-align: center;">Szczegóły</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($paged_logs)): $row_idx = 0; ?>
                    <?php foreach ($paged_logs as $log_line): 
                        $parts = explode(' | ', $log_line);
                        if (count($parts) !== 7) { continue; }
                        
                        $row_idx++;
                        $time        = htmlspecialchars(trim($parts[0]), ENT_QUOTES, 'UTF-8');
                        $user        = htmlspecialchars(trim($parts[1]), ENT_QUOTES, 'UTF-8');
                        $ip          = htmlspecialchars(trim($parts[2]), ENT_QUOTES, 'UTF-8');
                        $section     = htmlspecialchars(trim($parts[3]), ENT_QUOTES, 'UTF-8'); 
                        $action_type = htmlspecialchars(trim($parts[4]), ENT_QUOTES, 'UTF-8'); 
                        $object_type = htmlspecialchars(trim($parts[5]), ENT_QUOTES, 'UTF-8'); 
                        $history_raw = trim($parts[6]); 

                        $badgeClass = 'badge-default';
                        if (mb_stripos($action_type, 'Edycja') !== false || mb_stripos($action_type, 'Aktualizacja') !== false) {
                            $badgeClass = 'badge-blue';
                        } elseif (mb_stripos($action_type, 'Dodanie') !== false) {
                            $badgeClass = 'badge-green';
                        } elseif (mb_stripos($action_type, 'Usunięcie') !== false) {
                            $badgeClass = 'badge-red';
                        }

                        $historyData = json_decode($history_raw, true);
                    ?>
                    <tr>
                        <td class="log-timestamp"><?= $time ?></td>
                        <td class="log-user" title="IP: <?= $ip ?>"><?= $user ?></td>
                        <td class="log-section"><?= $section ?></td>
                        <td><span class="log-badge <?= $badgeClass ?>"><?= $action_type ?></span></td>
                        <td><span class="log-badge badge-object" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; display: inline-block;" title="<?= $object_type ?>"><?= $object_type ?></span></td>
                        <td style="text-align: center;">
                            <button type="button" class="btn-toggle-details" onclick="toggleLogDetails('details-<?= $row_idx ?>', this)">👁 Pokaż</button>
                        </td>
                    </tr>
                    
                    <tr id="details-<?= $row_idx ?>" class="details-row">
                        <td colspan="6" class="details-cell">
                            <div style="padding: 10px 0;">
                                <h4 style="margin: 0 0 10px 145px; color: #1e293b; font-size: 0.9rem;">Pełna lista modyfikacji pól:</h4>
                                <?php if (is_array($historyData) && !empty($historyData)): ?>
                                    <?php foreach ($historyData as $fieldName => $values): 
                                        $oldVal = htmlspecialchars($values['old'] ?? '', ENT_QUOTES, 'UTF-8');
                                        $newVal = htmlspecialchars($values['new'] ?? '', ENT_QUOTES, 'UTF-8');
                                        if ($oldVal !== $newVal):
                                    ?>
                                        <div class="history-item">
                                            <span class="history-label"><?= htmlspecialchars(ucfirst($fieldName)) ?>:</span>
                                            <div class="history-box">
                                                <span style="text-decoration: line-through; color: #94a3b8;"><?= $oldVal !== '' ? $oldVal : '<i>brak</i>' ?></span>
                                                <span class="history-arrow">→</span>
                                                <span style="color: #16a34a; font-weight: bold;"><?= $newVal !== '' ? $newVal : '<i>puste</i>' ?></span>
                                            </div>
                                        </div>
                                    <?php endif; endforeach; ?>
                                <?php else: ?>
                                    <p style="color: #94a3b8; margin: 0 0 0 145px; font-style: italic; font-size: 0.85rem;">Brak danych szczegółowych o zmianach (operacja strukturalna).</p>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #999999; padding: 40px; font-style: italic;">Brak logów spełniających wybrane kryteria filtrów.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PASEK STRONICOWANIA (PAGINACJA HTML + PHP) -->
    <div class="pagination-container">
        <div>
            Pokazano wpisy <strong><?= min($start_index + 1, $total_filtered_items) ?> - <?= min($start_index + $per_page, $total_filtered_items) ?></strong> z <strong><?= $total_filtered_items ?></strong>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <div class="pagination-links">
                <!-- Strzałka wstecz -->
                <?php if ($current_page > 1): ?>
                    <a href="<?= buildPaginationUrl($current_page - 1) ?>" class="pagination-link">« Poprzednia</a>
                <?php else: ?>
                    <span class="pagination-link disabled">« Poprzednia</span>
                <?php endif; ?>

                <!-- Numery stron -->
                <?php for ($page_i = 1; $page_num = $page_i, $page_i <= $total_pages; $page_i++): ?>
                    <a href="<?= buildPaginationUrl($page_num) ?>" class="pagination-link <?= $current_page === $page_num ? 'active' : '' ?>">
                        <?= $page_num ?>
                    </a>
                <?php endfor; ?>

                <!-- Strzałka w przód -->
                <?php if ($current_page < $total_pages): ?>
                    <a href="<?= buildPaginationUrl($current_page + 1) ?>" class="pagination-link">Następna »</a>
                <?php else: ?>
                    <span class="pagination-link disabled">Następna »</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleLogDetails(rowId, button) {
    const targetRow = document.getElementById(rowId);
    if (!targetRow) return;
    
    const isOpen = targetRow.classList.toggle('open');
    if (isOpen) {
        button.textContent = "✕ Ukryj";
        button.style.background = "#475569";
        button.style.color = "#ffffff";
    } else {
        button.textContent = "👁 Pokaż";
        button.style.background = "#f1f5f9";
        button.style.color = "#475569";
    }
}
</script>
