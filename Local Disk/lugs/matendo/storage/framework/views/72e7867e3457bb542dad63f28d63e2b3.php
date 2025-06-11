<h2 class="text-xl font-semibold mb-6">Platform Settings</h2>
<form class="max-w-xl space-y-6 bg-white p-6 rounded-lg shadow" method="POST" action="{ route('admin.settings.update') }}">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    
    <div>
        <label for="platform-name" class="block text-gray-700 font-semibold mb-2">Platform Name</label>
        <input type="text" id="platform-name" name="platform_name" class="w-full border-gray-300 rounded px-3 py-2" value="{ old('platform_name', $settings['platform_name'] ?? 'Matendo Medic') }}">
        <?php $__errorArgs = ['platform_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    
    <div>
        <label for="admin-email" class="block text-gray-700 font-semibold mb-2">Admin Email</label>
        <input type="email" id="admin-email" name="admin_email" class="w-full border-gray-300 rounded px-3 py-2" value="{ old('admin_email', $settings['admin_email'] ?? 'admin@matendomedic.com') }}">
        <?php $__errorArgs = ['admin_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    
    <div>
        <label for="default-language" class="block text-gray-700 font-semibold mb-2">Default Language</label>
        <select id="default-language" name="default_language" class="w-full border-gray-300 rounded px-3 py-2">
            <option value="en" <?php echo e((old('default_language', $settings['default_language'] ?? 'en') == 'en') ? 'selected' : ''); ?>>English</option>
            <option value="fr" <?php echo e((old('default_language', $settings['default_language'] ?? 'en') == 'fr') ? 'selected' : ''); ?>>French</option>
            <option value="sw" <?php echo e((old('default_language', $settings['default_language'] ?? 'en') == 'sw') ? 'selected' : ''); ?>>Swahili</option>
            <option value="es" <?php echo e((old('default_language', $settings['default_language'] ?? 'en') == 'es') ? 'selected' : ''); ?>>Spanish</option>
        </select>
        <?php $__errorArgs = ['default_language'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    
    <div>
        <label for="enable-notifications" class="inline-flex items-center">
            <input type="checkbox" id="enable-notifications" name="enable_notifications" value="1" <?php echo e(old('enable_notifications', $settings['enable_notifications'] ?? true) ? 'checked' : ''); ?> class="form-checkbox">
            <span class="ml-2 text-gray-700 font-semibold">Enable Email Notifications</span>
        </label>
    </div>
    
    <div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded transition">Save Settings</button>
    </div>
</form><?php /**PATH D:\lugs\matendo\resources\views/admin/partials/_settings.blade.php ENDPATH**/ ?>