<?php
// session_start();
include('../include/connect.php');

$search_query = "";
$search_year = "";
$search_month = "";
$search_date = ""; 
$search_period = "monthly"; 

if (isset($_POST['search'])) {
    $search_query = mysqli_real_escape_string($con, $_POST['search_query']);
    $search_year = mysqli_real_escape_string($con, $_POST['search_year']);
    $search_month = mysqli_real_escape_string($con, $_POST['search_month']);
    $search_period = mysqli_real_escape_string($con, $_POST['search_period']);
    if (!empty($_POST['search_date'])) {
        $search_date = mysqli_real_escape_string($con, $_POST['search_date']);
    }
}

$sql = "SELECT * FROM user_orders WHERE (order_status = 'Shipped' OR order_status = 'Arrived')";
$conditions = [];
$params = [];
$types = "";

if (!empty($search_query)) {
    $conditions[] = "invoice_number LIKE ?";
    $params[] = "%" . $search_query . "%";
    $types .= "s";
}

switch ($search_period) {
    case 'daily':
        if (!empty($search_date)) {
            $conditions[] = "DATE(order_date) = ?"; 
            $params[] = $search_date;
            $types .= "s";
        }
        break;
    case 'monthly':
        if (!empty($search_year)) {
            $conditions[] = "YEAR(order_date) = ?";
            $params[] = $search_year;
            $types .= "i";
        }
        if (!empty($search_month)) {
            $conditions[] = "MONTH(order_date) = ?";
            $params[] = $search_month;
            $types .= "i";
        }
        break;
    case 'yearly':
        if (!empty($search_year)) {
            $conditions[] = "YEAR(order_date) = ?";
            $params[] = $search_year;
            $types .= "i";
        }
        break;
}

if (count($conditions) > 0) {
    $sql .= " AND " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY order_date DESC";

$stmt = mysqli_prepare($con, $sql);
if (count($params) > 0) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$sql_run = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 2px solid #000; padding: 8px; text-align: center; vertical-align: middle; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #ddd; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h3 class="text-center">Payment Report</h3>

        <form method="POST" action="" class="mb-4 mt-4">
            <div class="form-row align-items-end">
                <div class="form-group col-md-3">
                    <label for="search_query">Invoice Number</label>
                    <input type="text" id="search_query" name="search_query" class="form-control" placeholder="Search by invoice..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>

                <div class="form-group col-md-2">
                    <label for="search_period">Period</label>
                    <select id="search_period" name="search_period" class="form-control">
                        <option value="daily" <?php echo ($search_period == 'daily') ? 'selected' : ''; ?>>Daily</option>
                        <option value="monthly" <?php echo ($search_period == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                        <option value="yearly" <?php echo ($search_period == 'yearly') ? 'selected' : ''; ?>>Yearly</option>
                    </select>
                </div>

                <div class="form-group col-md-3" id="date_filter_group" style="display:none;">
                    <label for="search_date">Select Date</label>
                    <input type="date" id="search_date" name="search_date" class="form-control" value="<?php echo htmlspecialchars($search_date); ?>">
                </div>
                
                <div class="form-group col-md-2" id="year_filter_group">
                    <label for="search_year">Year</label>
                    <select id="search_year" name="search_year" class="form-control">
                        <option value="">All Years</option>
                        <?php
                        $years_query = "SELECT DISTINCT YEAR(order_date) AS year FROM user_orders WHERE order_status IN ('Shipped', 'Success - Arrived') ORDER BY year DESC";
                        $years_result = mysqli_query($con, $years_query);
                        while ($row = mysqli_fetch_assoc($years_result)) {
                            $year = $row['year'];
                            $selected = ($year == $search_year) ? 'selected' : '';
                            echo "<option value=\"$year\" $selected>$year</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group col-md-2" id="month_filter_group">
                    <label for="search_month">Month</label>
                    <select id="search_month" name="search_month" class="form-control">
                        <option value="">All Months</option>
                        <?php
                        $months = [
                            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June',
                            '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
                        ];
                        foreach ($months as $num => $name) {
                            $selected = ($num == $search_month) ? 'selected' : '';
                            echo "<option value=\"$num\" $selected>$name</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <button class="btn btn-primary btn-block" type="submit" name="search">Search</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered mt-4">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Invoice Number</th>
                    <th>Amount</th>
                    <th>Courier</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class='text-center'>
                <?php if ($sql_run && mysqli_num_rows($sql_run) > 0): $no = 1 ?>
                    <?php while ($row_data = mysqli_fetch_assoc($sql_run)): ?>
                    <tr class='font-weight-bold'>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row_data['invoice_number']); ?></td>
                        <td>Rp. <?php echo number_format($row_data['amount_due'], 0, ',','.'); ?></td>
                        <td><?php echo htmlspecialchars($row_data['ekspedisi']); ?></td>
                        <td><?php echo date("d M Y, H:i", strtotime($row_data['order_date'])); ?></td>
                        <td><?php echo htmlspecialchars($row_data['order_status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-danger font-weight-bold p-4">No records found matching your criteria.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<script>
$(document).ready(function() {
    function toggleFilters() {
        var period = $('#search_period').val();
        if (period === 'daily') {
            $('#date_filter_group').show();
            $('#month_filter_group').hide();
            $('#year_filter_group').hide();
        } else if (period === 'monthly') {
            $('#date_filter_group').hide();
            $('#month_filter_group').show();
            $('#year_filter_group').show();
        } else if (period === 'yearly') {
            $('#date_filter_group').hide();
            $('#month_filter_group').hide();
            $('#year_filter_group').show();
        }
    }

    // Jalankan fungsi saat halaman pertama kali dimuat
    toggleFilters();

    // Jalankan fungsi setiap kali dropdown periode diubah
    $('#search_period').on('change', function() {
        toggleFilters();
    });
});
</script>
</body>
</html>