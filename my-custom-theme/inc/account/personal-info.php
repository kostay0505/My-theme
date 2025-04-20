<?php
/**
 * Личный кабинет → Личная информация
 * Отображается внутри page-account.php
 */

$current_user = wp_get_current_user();
?>

<form class="personal-form" method="post" enctype="multipart/form-data">

  <div class="columns">
    <!-- Левая колонка — аватар + ФИО + e‑mail -->
    <div class="col col-left">
      <div class="avatar">
        <?php echo get_avatar( $current_user->ID, 120 ); ?>
        <input type="file" name="avatar" accept="image/*">
      </div>

      <label>Имя*
        <input type="text" name="first_name" value="<?php echo esc_attr( $current_user->first_name ); ?>" required>
      </label>

      <label>Фамилия*
        <input type="text" name="last_name" value="<?php echo esc_attr( $current_user->last_name ); ?>" required>
      </label>

      <label>Email*
        <input type="email" name="user_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" required>
      </label>
    </div>

    <!-- Правая колонка — смена пароля -->
    <div class="col col-right">
      <h3>Смена пароля</h3>

      <label>Новый пароль
        <input type="password" name="pass1">
      </label>

      <label>Подтвердите пароль
        <input type="password" name="pass2">
      </label>
    </div>
  </div>

  <?php wp_nonce_field( 'save_personal', '_personal_nonce' ); ?>
  <button type="submit" class="btn full">Сохранить</button>
</form>

<?php
/* ========== Обработка формы (упрощённо, без загрузки avatar) ========== */
if ( isset( $_POST['_personal_nonce'] ) && wp_verify_nonce( $_POST['_personal_nonce'], 'save_personal' ) ) {

  $update = [
    'ID'         => $current_user->ID,
    'first_name' => sanitize_text_field( $_POST['first_name'] ),
    'last_name'  => sanitize_text_field( $_POST['last_name'] ),
    'user_email' => sanitize_email( $_POST['user_email'] ),
  ];

  if ( ! empty( $_POST['pass1'] ) && $_POST['pass1'] === $_POST['pass2'] ) {
    $update['user_pass'] = $_POST['pass1'];
  }

  wp_update_user( $update );
  echo '<p style="color:green">Данные сохранены!</p>';
}
