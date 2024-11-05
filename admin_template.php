<?php
global $page_slug;
$if_price_is_zero_shortcode_field = get_option('if_price_is_zero_shortcode_field'); 
$if_price_is_zero_button_name = get_option('if_price_is_zero_button_name'); 
$if_price_is_zero_catalog_on = get_option('if_price_is_zero_catalog_on'); 

?>

<div class="wrap">
    <h1> <?php echo get_admin_page_title() ?> </h1>
    <p>Разрабочик — <a href="https://github.com/edgar010797">Edgar Podosyan</a></p>
    <p><strong>Плагин работает только с простыми и вариативными товарами.</strong></p>
    <form name="if_price_is_zero_admin_options_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $page_slug; ?>&amp;updated=true">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label>Вставьте шорткод контактной формы из плагина Contact Form 7</label>
                    </th>
                    <td>
                        <input placeholder="[contact-form-7 id='1c61abc' title='Оставить заявку']"
                                   name="if_price_is_zero_shortcode_field" type="text"
                                   value="<?php echo str_replace(["\\"], "", htmlspecialchars($if_price_is_zero_shortcode_field)); ?>" 
                                   required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label>Название кнопки вызова контактной формы</label>
                    </th>
                    <td>
                        <input placeholder="Оставить заявку"
                                   name="if_price_is_zero_button_name" type="text"
                                   value="<?php echo str_replace(["\\"], "", htmlspecialchars($if_price_is_zero_button_name)); ?>"
                                   required>
                    </td>
                </tr>  
                <tr>
                    <th scope="row">
                        <label>Включить режим каталога</label>
                    </th>
                    <td>
                        <input name="if_price_is_zero_catalog_on" type="checkbox" <?php echo $if_price_is_zero_catalog_on == 'on' ? 'checked' : ''; ?>>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Сохранить изменения"></p>
    </form>
</div>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    echo "<div style='padding-left: 20px;color: green;margin-bottom: 40px;'><p style='font-size: 18px;font-weight: bold;'>Настройки сохранены!</p></div>";
} ?>
