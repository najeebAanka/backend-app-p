     <?php if($message = Session::get('error')): ?>
                                          <div class="alert alert-warning noborder text-center weight-400 nomargin noradius">
                                         
                                              <strong><?php echo e($message); ?></strong>
                                          </div>
                                          <?php endif; ?>

                                          <?php if(count($errors) > 0): ?>
                                          <div class="alert alert-danger noborder text-center weight-400 nomargin noradius">
                                              <ul>
                                                  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                  <li><?php echo e($error); ?></li>
                                                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                              </ul>
                                          </div>
                                          <?php endif; ?>
                                          
                                          
                                            
     <?php if($message = Session::get('message')): ?>
                                          <div class="alert alert-success noborder text-center weight-400 nomargin noradius">
                                         
                                              <strong><?php echo e($message); ?></strong>
                                          </div>
                                          <?php endif; ?><?php /**PATH C:\wamp64\www\test\resources\views/dashboard/shared/messages.blade.php ENDPATH**/ ?>