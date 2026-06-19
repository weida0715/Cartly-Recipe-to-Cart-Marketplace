</main>
<footer class="site-footer">
  <div class="container footer-content">
    <section class="footer-brand" aria-labelledby="footer-brand-title">
      <h2 id="footer-brand-title">Cartly</h2>
      <p>Recipe-to-Cart Marketplace for fresh ingredients from local merchants.</p>
    </section>

    <nav class="footer-links" aria-label="Footer navigation">
      <div>
        <h3>Platform</h3>
        <a href="<?= BASE_URL ?>/">Home</a>
        <a href="<?= BASE_URL ?>/products">Marketplace</a>
        <a href="<?= BASE_URL ?>/recipes">Recipes</a>
      </div>
      <div>
        <h3>Customer Support</h3>
        <a href="<?= BASE_URL ?>/cart">Shopping Cart</a>
        <a href="<?= BASE_URL ?>/orders">Order Help</a>
        <a href="mailto:support@cartly.local">Help Center</a>
      </div>
      <div>
        <h3>Merchants</h3>
        <a href="<?= BASE_URL ?>/dashboard">Become a Merchant</a>
        <a href="<?= BASE_URL ?>/merchant">Merchant Portal</a>
        <a href="<?= BASE_URL ?>/merchant/store">Store Profile</a>
      </div>
      <div>
        <h3>Connect</h3>
        <a href="mailto:hello@cartly.local">Contact Cartly</a>
        <a href="mailto:partnerships@cartly.local">Partnerships</a>
        <a href="mailto:feedback@cartly.local">Send Feedback</a>
      </div>
    </nav>

    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> Cartly. All rights reserved.</p>
      <p>Built for recipe planning, local shopping, and smarter carts.</p>
    </div>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
<script src="<?= ASSET_URL ?>/js/app.js"></script>
</body>
</html>
