
            <!-- Dashboard Content -->
            <main class="p-4">
                <!-- Stats Cards -->
                <div class="grid gridwe grid-cols-5 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total orders</p>
                                <p class="text-2xl font-bold"><?= $stats['total'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Completed</p>
                                <p class="text-2xl font-bold"><?= $stats['completed'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Delivering</p>
                                <p class="text-2xl font-bold"><?= $stats['in_progress'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Ready for delivery</p>
                                <p class="text-2xl font-bold"><?= $stats['pending'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                                <i class="fa-solid fa-money-check-dollar"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total balance</p>
                                <p class="text-2xl font-bold"><?= round($stats['total_earning'], 2) ?> Birr</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Today's Deliveries -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="p-4 border-b border-gray-200">
                                <h3 style="display: flex; justify-content: space-between;" class="font-semibold text-lg flex items-center">
                                    <span>
                                        <i class="fas fa-calendar-day mr-2 text-indigo-600"></i>
                                        Today's Deliveries </span>
                                    <span ><?php if (!empty($today_del)): 
                                         echo count($today_del) . 'orders found';
                                    else: echo 'no orders found!';
                                    endif;
                                    ?></span>
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <?php if (empty($today_deliveries)): ?>
                                    <div class="p-4 text-center text-gray-500">
                                        No deliveries scheduled for today
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($today_del as $delivery): ?>
                                        <div class="delivery-card p-4 hover:bg-gray-50">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="flex items-center mb-1">
                                                        <span class="font-medium mr-2">Order #<?= $delivery['order_id'] ?></span>
                                                        <span class="status-badge status-<?= str_replace(' ', '-', strtolower($delivery['status'])) ?>">
                                                            <?= $delivery['status'] ?>
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mb-1">
                                                        <i class="fas fa-store mr-1"></i>
                                                        <?= htmlspecialchars($delivery['restaurant_name']) ?>
                                                    </p>
                                                    <p class="text-sm text-gray-600 mb-1">
                                                        <i class="fas fa-user mr-1"></i>
                                                        <?= htmlspecialchars($delivery['customer_name']) ?>
                                                    </p>
                                                    <p class="text-sm text-gray-600">
                                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                                        <?= htmlspecialchars($delivery['delivery_address']) ?>
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm text-gray-500 mb-2">
                                                        <?= date('h:i A', strtotime($delivery['order_date'])) ?>
                                                    </p>
                                                    <?php if ($delivery['status'] === 'Pending'): ?>
                                                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm">
                                                            Start Delivery
                                                        </button>
                                                    <?php elseif ($delivery['status'] === 'Out for Delivery' || $delivery['status'] === 'On the Way'): ?>
                                                        <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                                            Mark Delivered
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Performance and Map -->
                    <div class="space-y-6">
                        <!-- Recent Deliveries -->
                        <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
                            <div class="p-4 border-b border-gray-200">
                            <h3 style="justify-content: space-between;" class="font-semibold text-lg flex items-center">
                                    <span>
                                        <i class="fas fa-calendar-day mr-2 text-indigo-600"></i>
                                        Recent Deliveries 
                                    </span>
                                    <span ><?php if (!empty($recent_del)): 
                                         echo count($recent_del). ' orders found.';
                                    else: echo 'no orders found';
                                    endif;
                                    ?></span>
                                </h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurant</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php if (empty($recent_del)): ?>
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No recent deliveries found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($recent_del as $delivery): ?>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $delivery['order_id'] ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($delivery['restaurant_name']) ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($delivery['customer_name']) ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M j, Y', strtotime($delivery['order_date'])) ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="status-badge status-<?= str_replace(' ', '-', strtolower($delivery['status'])) ?>">
                                                            <?= $delivery['status'] ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?=$delivery['delivery_person_fee'] ?> birr</td>

                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- otrder status chart -->
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="font-semibold text-lg flex items-center">
                                    <i class="fas fa-map-marked-alt mr-2 text-indigo-600"></i>
                                    Order status chart
                                </h3>
                            </div>
                            <div class="map-container p-4">
                            </div>
                        </div>
                    </div>
                </div>                
            </main>