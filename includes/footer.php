    </div>
    <footer class="bg-blue-600 text-white mt-auto">
        <div class="container mx-auto px-6 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="animate__animated animate__fadeIn">
                    <h3 class="text-lg font-semibold mb-4">PharmaCare</h3>
                    <p class="text-sm text-gray-100">
                        Your trusted pharmacy management solution for efficient healthcare delivery.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo $base_url; ?>about.php" class="hover:text-gray-200 transition">About Us</a></li>
                        <li><a href="<?php echo $base_url; ?>contact.php" class="hover:text-gray-200 transition">Contact</a></li>
                        <li><a href="<?php echo $base_url; ?>privacy.php" class="hover:text-gray-200 transition">Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-phone"></i>
                            <span>+9801837292</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-envelope"></i>
                            <span>support@pharmacare.com</span>
                        </li>
                    </ul>
                </div>

                <!-- Social Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-gray-200 transition">
                            <i class="fab fa-facebook fa-lg"></i>
                        </a>
                        <a href="#" class="hover:text-gray-200 transition">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                        <a href="#" class="hover:text-gray-200 transition">
                            <i class="fab fa-linkedin fa-lg"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-blue-500 mt-8 pt-8 text-center text-sm">
                <p>&copy; <?php echo date('Y'); ?> PharmaCare. All rights reserved.</p>
                <?php if (isset($_SESSION['last_login'])): ?>
                <p class="mt-2 text-sm text-gray-300">
                    Last login: <?php echo date('Y-m-d H:i:s', $_SESSION['last_login']); ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </footer>
</body>
</html> 