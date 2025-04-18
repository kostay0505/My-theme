<?php
// footer.php
?>
<footer class="site-footer">
  <div class="footer-inner container">

    <div class="footer-columns">
      <!-- Колонка: меню ссылок -->
      <div class="footer-column footer-links">
        <ul>
          <li><a href="#">Оборудование</a></li>
          <li><a href="#">Инсталляции</a></li>
          <li><a href="#">Новости</a></li>
          <li><a href="#">Вопросы</a></li>
          <li><a href="#">Контакты</a></li>
          <li><a href="#">О нас</a></li>
        </ul>
      </div>

      <!-- Колонка: соцсети -->
      <div class="footer-column footer-socials">
        <a href="#" target="_blank">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/tg.png" alt="Telegram">
        </a>
        <a href="#" target="_blank">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vk.png" alt="VK">
        </a>
        <a href="#" target="_blank">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/wa.png" alt="WhatsApp">
        </a>
      </div>

      <!-- Колонка: подписка -->
      <div class="footer-column footer-subscribe">
        <h3>Подписка на рассылку</h3>
        <p>Будьте в курсе горячих предложений и новостей</p>
        <form action="#" method="post">
          <input 
            type="email" 
            name="subscribe-email"
            placeholder="Введите e-mail"
            required
          >
          <button type="submit">Подписаться</button>
        </form>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; <?php echo date('Y'); ?> My Custom Theme</p>
    </div>

  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
