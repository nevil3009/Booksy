<link rel="stylesheet" href="wishlist.css">
<?php include 'Pages/header.php'; ?>

<div class="page-navigation-wrapper">
    <div class="book1">
        <img src="/Booksy/AssetS/shop-default/book1.png" alt="book">
    </div>
    <div class="book2">
        <img src="/Booksy/AssetS/shop-default/book2.png" alt="book">
    </div>
    <div class="container">
        <div class="page-heading">
            <h1>Wishlist</h1>
            <div class="page-header">
                <ul class="custom-navigation">
                    <li><a href="/Booksy/index.php">Home</a></li>
                    <li><i class="bi bi-chevron-right"></i></li>
                    <li>wishlist</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="table-responsive product-table">
        <table class="table table-borderless align-middle">

            <thead>
                <tr style="font-size: 24px; font-weight: 600; color: #012e4a; padding-bottom: 20px; padding-left: 0;">
                    <th scope="col">Product</th>
                    <th scope="col">Price</th>
                    <th scope="col">Stock</th>
                    <th scope="col">Subtotal</th>
                </tr>

            </thead>
            <tbody>

                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="/Booksy/Assets/remove-icon.svg" alt="Book" class="cancle-img">
                            <img src="/Booksy/Assets/cart/01.png" alt="Book" class="product-img">
                            <span class="product-name">Simple Things You To Save Book</span>
                        </div>
                    </td>
                    <td><span class="price-text">$30.00</span></td>
                    <td><span class="in-stock">In Stock</span></td>
                    <td><span class="price-text">$120.00</span></td>
                </tr>

                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="/Booksy/Assets/remove-icon.svg" alt="Book" class="cancle-img">
                            <img src="/Booksy/Assets/cart/02.png" alt="Book" class="product-img">
                            <span class="product-name">Apple iPad With Retina Display</span>
                        </div>
                    </td>
                    <td><span class="price-text">$39.00</span></td>
                    <td><span class="in-stock">In Stock</span></td>
                    <td><span class="price-text">$120.00</span></td>
                </tr>

    
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="/Booksy/Assets/remove-icon.svg" alt="Book" class="cancle-img">
                            <img src="/Booksy/Assets/cart/03.png" alt="Book" class="product-img">
                            <span class="product-name">Flovely And Unicom Erna</span>
                        </div>
                    </td>
                    <td><span class="price-text">$19.00</span></td>
                    <td><span class="out-stock">Out Of Stock</span></td>
                    <td><span class="price-text">$120.00</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<?php include 'Pages/footer.php'; ?>