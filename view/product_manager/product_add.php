<?php include '../view/shared/header.php'; ?>
<main>
    <h1>Add Product</h1>
    <form action="index.php" method="post" id="add_product_form">
        <input type="hidden" name="action" value="add_product">

        <label>Category:</label>
        <select name="category_id">
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['categoryID']; ?>">
                    <?php echo $category['categoryName']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label>Code:</label>
        <input type="input" name="code" 
               value="<?php echo htmlspecialchars(trim(strtoupper($code))); ?>">
               <?php echo $fields->getField('code')->getHTML(); ?>
        <br>

        <label>Name:</label>
        <input type="input" name="name"
               value="<?php echo htmlspecialchars(trim($name)); ?>">
               <?php echo $fields->getField('name')->getHTML(); ?>
        <br>

        <label>List Price:</label>
        <input type="input" name="price"
               value="<?php echo htmlspecialchars(trim($price)); ?>">
               <?php echo $fields->getField('price')->getHTML(); ?>
        <br>

        <label>&nbsp;</label>
        <input type="submit" value="Add Product" />
        <br>
    </form>
    <p class="last_paragraph">
        <a href=".?action=list_products">View Product List</a>
    </p>

</main>
<?php include '../view/shared/footer.php'; ?>