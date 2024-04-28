
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Messages</title>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap-social.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/components.css') }}">

  <link rel="stylesheet" href="{{ asset('global/toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap4-toggle.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/dev.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/tagify.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap-tagsinput.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/fontawesome-iconpicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/clockpicker/dist/bootstrap-clockpicker.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/datetimepicker/jquery.datetimepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/iziToast.min.css') }}">

  <script src="{{ asset('global/js/jquery-3.7.1.min.js') }}"></script>

<script>
    var PUSHER_APP_KEY = "{{ $setting->pusher_app_key }}";
    var PUSHER_APP_CLUSTER = "{{ $setting->pusher_app_cluster }}";
    var ROOT_ROUTE = "{{ route('home') }}";
    var PUSHER_AUTH_ROUTE = ROOT_ROUTE + "/broadcasting/auth";
</script>
  @vite(['resources/js/app.js'])

<!-- /END GA -->
</head>

<body>
  <div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Chat Box : I am {{ $auth_user->name }}</h1>
          </div>



          <div class="section-body">

            <form action="{{ route('send-new-message') }}" method="POST">
                @csrf
                <div class="form-group">
                    <select name="receiver_id" id="" class="form-control">
                        <option value="">Select User</option>
                        @foreach ($user_list as $list_item)
                            <option value="{{ $list_item->id }}">{{ $list_item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <input type="text" name="message" class="form-control">
                </div>

                <button class="btn btn-primary">Send Message</button>

            </form>

          </div>

          <br>
          <br>
          <br>
          <br>
          <br>
          <br>
          <br>

          <div class="section-body">


            <div class="row">
              <div class="col-md-4">
                <div class="card">

                  <div class="card-body">
                    <ul class="list-unstyled list-unstyled-border" id="contact_list">
                        @foreach ($contact_list as $contact_item)
                            <li class="media" onclick="loadChatBody('{{ $contact_item->id }}')">
                                <img alt="image" class="mr-3 rounded-circle" width="50" src="https://www.gravatar.com/avatar/75d23af433e0cea4c0e45a56dba18b30">
                                <div class="media-body">
                                    <div class="mt-0 mb-1 font-weight-bold">{{ $contact_item?->name }} - <b class="new_message_count_{{ $contact_item->id }} {{ $contact_item?->new_message == 0 ? 'd-none' : '' }}">{{ $contact_item?->new_message }}</b></div>

                                    <div class="text-small font-weight-600 text-muted active_status_{{ $contact_item->id }}"><i class="fas fa-circle"></i> Offline </div>

                                </div>
                            </li>
                        @endforeach

                    </ul>
                  </div>
                </div>
              </div>
                <div class="col-md-8">
                    <input type="hidden" id="active_contact_id">
                    <div class="card chat-box" id="mychatbox">


                    </div>
                </div>

            </div>
          </div>
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2018 <div class="bullet"></div> Design By <a href="https://nauval.in/">Muhamad Nauval Azhar</a>
        </div>
        <div class="footer-right">

        </div>
      </footer>
    </div>
  </div>


  <script src="{{ asset('backend/js/popper.min.js') }}"></script>
  <script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>


  <script src="{{ asset('global/toastr/toastr.min.js') }}"></script>


  <script>
      @if (Session::has('messege'))
          var type = "{{ Session::get('alert-type', 'info') }}"
          switch (type) {
              case 'info':
                  toastr.info("{{ Session::get('messege') }}");
                  break;
              case 'success':
                  toastr.success("{{ Session::get('messege') }}");
                  break;
              case 'warning':
                  toastr.warning("{{ Session::get('messege') }}");
                  break;
              case 'error':
                  toastr.error("{{ Session::get('messege') }}");
                  break;
          }
      @endif
  </script>

  @if ($errors->any())
      @foreach ($errors->all() as $error)
          <script>
              toastr.error('{{ $error }}');
          </script>
      @endforeach
  @endif

  <script type="module">
        let auth_user_id = "{{ $auth_user->id }}";

        let contact_list = <?php echo json_encode($contact_list); ?>;

        Echo.channel(`private-livechat.${auth_user_id}`)
            .listen('.live-chat', (res) => {
                toastr.success('You have received new message');

                /*at first need to check the sender id is already available in contact list or not, if not need to push the contact item in list*/

                let is_exist = contact_list.find(list_item => list_item.id == res.sender_id);

                if(is_exist){
                    let active_contact_id = $("#active_contact_id").val()

                    /*if selected user and new message sender id is same, request to latest message list and append on message body */

                    if(active_contact_id == res.sender_id){
                        $.ajax({
                            type : 'get',
                            url : "{{ url('/load-latest-message') }}" + "/" + active_contact_id,
                            success : function(response){
                                $(".chat-content").html(response);
                                scrollToBottomFunc();
                            },
                            error : function(error){
                                alert("server error")
                            }
                        })
                    }else{
                        let new_message_count = $(".new_message_count_"+res.sender_id).html();
                        new_message_count = parseInt(new_message_count) + parseInt(1);
                        $(".new_message_count_"+res.sender_id).html(new_message_count)
                        $(".new_message_count_"+res.sender_id).removeClass('d-none')

                    }
                }else{
                    /*get new sender info and push to existing list*/
                    $.ajax({
                            type : 'get',
                            url : "{{ url('/get-new-contact-sender') }}" + "/" + res.sender_id,
                            success : function(response){

                                let contact_author = response.contact_author

                                contact_list.push({
                                        id : contact_author.id,
                                        name : contact_author.name,
                                        email : contact_author.email,
                                        last_message : 'last_message',
                                        image : contact_author.image,
                                        new_message : 'new_message',
                                    });

                                   let new_list_item = `<li class="media" onclick="loadChatBody('${contact_author.id}')">
                                        <img alt="image" class="mr-3 rounded-circle" width="50" src="https://www.gravatar.com/avatar/75d23af433e0cea4c0e45a56dba18b30">
                                        <div class="media-body">
                                            <div class="mt-0 mb-1 font-weight-bold">${contact_author.name} - <b class="new_message_count_${contact_author.id}">1</b></div>
                                            <div class="text-success text-small font-600-bold"><i class="fas fa-circle"></i> Online </div>

                                            {{-- <div class="text-small font-weight-600 text-muted"><i class="fas fa-circle"></i> Offline</div> --}}
                                        </div>
                                    </li>`

                                    $("#contact_list").prepend(new_list_item);

                            },
                            error : function(error){
                                alert("server error")
                            }
                        })

                }

            });


            Echo.join(`trackactiveuser`)
                .here((users) => {

                    let user_id_sets = new Set(users.map(user => user.id));

                    contact_list.forEach(function(contact_item) {
                        let is_user_exist = user_id_sets.has(contact_item.id);
                        if(is_user_exist){
                            $(".active_status_"+ contact_item.id).addClass("font-600-bold text-success")
                            $(".active_status_"+ contact_item.id).removeClass("font-weight-600 text-muted")
                            $(".active_status_"+ contact_item.id).html(`<i class="fas fa-circle"></i> Online`)
                        }
                    });

                })
                .joining((user) => {

                    contact_list.forEach(function(contact_item) {
                        if(contact_item.id == user.id){
                            $(".active_status_"+ contact_item.id).addClass("font-600-bold text-success")
                            $(".active_status_"+ contact_item.id).removeClass("font-weight-600 text-muted")
                            $(".active_status_"+ contact_item.id).html(`<i class="fas fa-circle"></i> Online`)
                        }
                    });

                })
                .leaving((user) => {

                    contact_list.forEach(function(contact_item) {
                        if(contact_item.id == user.id){
                            $(".active_status_"+ contact_item.id).removeClass("font-600-bold text-success")
                            $(".active_status_"+ contact_item.id).addClass("font-weight-600 text-muted")
                            $(".active_status_"+ contact_item.id).html(`<i class="fas fa-circle"></i> Offline`)
                        }
                    });

                })
                .error((error) => {
                    console.error(error);
                });



  </script>
    <script>

        (function($) {
            "use strict";


        })(jQuery);


        function loadChatBody(contact_id){
            $(".new_message_count_"+contact_id).html(0)
            $(".new_message_count_"+contact_id).addClass('d-none')
            $.ajax({
                type : 'get',
                url : "{{ url('/load-message-box') }}" + "/" + contact_id,
                success : function(response){
                    $("#mychatbox").html(response);
                    scrollToBottomFunc();
                    $("#active_contact_id").val(contact_id)
                },
                error : function(error){
                    alert("server error")
                }
            })
        }

        function scrollToBottomFunc() {
            $('.chat-content').animate({
                scrollTop: $('.chat-content').get(0).scrollHeight
            }, 50);
        }
    </script>

  @stack('js')


</body>
</html>
