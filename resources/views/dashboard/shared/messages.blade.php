     @if ($message = Session::get('error'))
                                          <div class="alert alert-warning noborder text-center weight-400 nomargin noradius">
                                         
                                              <strong>{{ $message }}</strong>
                                          </div>
                                          @endif

                                          @if (count($errors) > 0)
                                          <div class="alert alert-danger noborder text-center weight-400 nomargin noradius">
                                              <ul>
                                                  @foreach($errors->all() as $error)
                                                  <li>{{ $error }}</li>
                                                  @endforeach
                                              </ul>
                                          </div>
                                          @endif
                                          
                                          
                                            
     @if ($message = Session::get('message'))
                                          <div class="alert alert-success noborder text-center weight-400 nomargin noradius">
                                         
                                              <strong>{{ $message }}</strong>
                                          </div>
                                          @endif