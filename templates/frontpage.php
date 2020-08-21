<!DOCTYPE html>
<html>

<head>
    <title><?php echo $title ?></title>
    <link href="https://getbootstrap.com/docs/4.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <main role="main" class="container">
        <div class="d-flex align-items-center p-3 my-3 text-white-50 bg-purple rounded shadow-sm">
            <div class="lh-100">
                <h6 class="mb-0 text-white lh-100">Small Book Shop</h6>
                <small>Sales Records</small>
            </div>
        </div>
        <div class="my-3 p-3 bg-white rounded shadow-sm">
            <h2 style="display: inline-block;">Book Sales</h2>

            <form class="form-inline my-2 my-lg-0" method="GET" action="" style="float: right;">
                <input type="hidden" name="uploadJson">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Upload JSON</button>
            </form>
            <div class="table-responsive">
                <div class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <form class="form-inline my-2 my-lg-0" method="GET" action="">
                        <label class="mb-0 text-white lh-100 mr-sm-2">Filters:</label>
                        <input class="form-control mr-sm-2" type="text" placeholder="Customer Name" aria-label="Customer Name" name="filter[customerName]">
                        <input class="form-control mr-sm-2" type="text" placeholder="Product Name" aria-label="Product Name" name="filter[productName]">
                        <input class="form-control mr-sm-2" type="float" placeholder="Product Price" aria-label="Product Price" name="filter[productPrice]">
                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                    </form>
                </div>
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Customer Mail</th>
                            <th>Book Name</th>
                            <th>Price</th>
                            <th>Version</th>
                            <th>Sales Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0;
                        foreach ($sales as $sale) : ?>
                            <tr>
                                <td><?php echo $sale->customerName; ?></td>
                                <td><?php echo $sale->mail; ?></td>
                                <td><?php echo $sale->productName; ?></td>
                                <td><?php echo $sale->price; ?></td>
                                <td><?php echo $sale->version; ?></td>
                                <td><?php echo date("D d M y H:i:s", strtotime($salesObj->compareVersion($sale->version, $sale->created_at))); ?></td>
                            </tr>
                        <?php
                            $total += $sale->price;
                        endforeach; ?>
                        <tr>
                            <td colspan="3" style="text-align: right;">Total Sale:</td>
                            <td colspan="3" style="font-weight: bold;"><?php echo $total; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </main>
</body>

</html>