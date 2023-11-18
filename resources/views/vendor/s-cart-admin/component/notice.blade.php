<style>
    .header-notify {
        width: 100%;
    }

    .header-notify span {
        font-weight: 600 !important;
        float: left;
        font-size: 18px;
        padding: 0px 12px !important;
    }

    .header-notify p {
        float: right;
        color: #3c8dbc;
    }

    .overflow-auto {
        padding: 3px !important;
        height: 400px;
        font-family: inherit;
    }

    .title {
        width: 90%;
        float: left;
        height: auto;
    }

    .icon-notify {
        float: right;
        color: #3c8dbc;
        margin-top: 5px;
    }

    .notification .title p a {
        font-weight: 600 !important;
        font-size: 15px;
        color: #3c8dbc !important;
        display: block;
        width: 100%;
    }

    .notification {
        width: 100%;
    }

    .notification p a {
        font-size: 15px;
        color: black !important;
    }

    .notification span a {
        font-size: 15px;
        color: #1877f2 !important;
    }

    .notification:hover {
        border: 0px #EBEDF0 solid !important;
        border-radius: 10px;
        background-color: #EBEDF0;
    }

    .notification-seen .title p a {
        color: #8993a0 !important;
    }

    .notification-seen p a {
        color: #8993a0 !important;
    }

    .notification-seen span a {
        color: #8993a0 !important;
    }

    .notification-seen .icon-notify {
        color: #8993a0;
    }

    .clear {
        clear: both;
    }

    .display-notify {
        display: none;
    }

    .count-number {
        background-color: #FA383E;
        color: white;
    }
    a#read-tick{
        padding-right: 20px;
    }
</style>
@php
    $route = URL::to('/');
    $newNotification = \App\Admin\Models\AdminNotification::getAdminNotification()->orderBy('id', 'DESC')->get();
@endphp
<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" id="avatar-notification">
        <i class="fa fa-bell"></i>
        <span id="number_notify" class="badge badge-warning navbar-badge count-number"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right overflow-auto" style="min-width: 320px; max-width: 340px">
        <div class="header-notify">
            <div><span>Thông báo</span></div>
            <div><p><a id="read-tick" href="#" onclick="readNotification()">Đánh dấu đã
                        đọc</a></p></div>
        </div>
        <br>
        <hr style="margin-top: 0.1rem; margin-bottom: 0.1rem">
        <div id="load-more">

        </div>
        <div id="remove-row">
            <a id="btn-more"></a>
        </div>
    </div>
</li>

{{--<script src="https://code.jquery.com/jquery-3.5.0.js"></script>--}}
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
{{--<script src='https://cdn.rawgit.com/admsev/jquery-play-sound/master/jquery.playSound.js'></script>--}}
<script>
    let currentPage = 0;
    let route = "{{ $route }}";

    $('#read-tick').on('click', function (e) {
        $('.notification').removeClass("notification-seen").addClass("notification-seen");
        $("#number_notify").addClass("display-notify");
    });
    function seeNotification(e, id, isSeen) {
        if (isSeen == 1){
            return;
        }
        readNotification(id);
        $('#notify-' + id).addClass('notification-seen');
    }
    function readNotification(idList = null) {
        let ids = null;
        let number = parseInt($('.count-number').text());
        let sum = 0;
        if (idList) {
            ids = $.isArray(idList) ? idList : [idList];
        }
        if (ids) {
            let count = ids.length;
            if (count < number) {
                sum = number - count;
            } else {
                sum = 0;
            }
        }
        let idc = '#notify-' + ids;
        $(idc).removeClass("notification-seen").addClass("notification-seen");
        $.ajax({
            method: 'post',
            url: '{{ sc_route_admin('admin_notify_history.readtick') }}',
            data: {
                "id": ids,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
            },
        });
        if (sum == 0) {
            $("#number_notify").addClass("display-notify");
        }
        $('.count-number').text(sum);
    }

    function loadMore(e) {
        $("#btn-more").html("Loading....");
        currentPage = currentPage + 1;
        $.ajax({
            url: '{{ sc_route_admin('admin_notify_history.get_list_paging') }}',
            method: "POST",
            data: {page: currentPage, _token: "{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if (data){
                    if(data.unread) {
                        $("#number_notify").text(data.unread);

                        $.toast({
                            heading: 'Bạn có ' + data.unread + ' thông báo chưa đọc',
                            icon: 'info',
                            showHideTransition: 'fade',
                            allowToastClose: true,
                            hideAfter: 1000,
                            stack: false,
                            position: 'top-right',
                            textAlign: 'left',
                            loader: true,
                            loaderBg: '#9EC600',
                        });
                    }
                    if (data.notifications){
                        for (let notification of data.notifications){
                            let html = '';
                            let seenClass = notification.seen == 1 ?"notification-seen":'';
                            let notifyTitle = notification.title;
                            let link = route + notification.link;
                            let icon = notification.icon ? notification.icon : "fas fa-bell" ;
                            html += '<div id="notify-' + notification.id + '" class="dropdown-item notification ' + seenClass + '" data-id="' + data.id + '" onclick="seeNotification(event, ' + notification.id + ',' + notification.seen +')">';
                            html += '<div class="title">';
                            html += '<p>';
                            html += '<strong><a href="' + link +'">' + notifyTitle + '</a></strong>';
                            html += '</p>';
                            html += '</div>';
                            html += '<div><i class="nav-icon ' + icon +' icon-notify"></i></div>';
                            html += '<div class="clear"></div>';
                            html += '<p><a href="' + link +'">' + notification.content + '</a></p>';
                            html += '<span><a href="' + link +'">' + notification.created_at + '</a></span>';
                            html += '</div>';
                            $('#load-more').append(html);
                        }
                    }

                    if (!data.more){
                        $('#btn-more').html('<a id="btn-more" href="#" class="dropdown-item dropdown-footer">Hết thông báo</a>');
                    } else{
                        $('#btn-more').html('<a id="btn-more" href="#" class="dropdown-item dropdown-footer" onclick="loadMore(event)">{{ sc_language_render('action.view_more') }}</a>');
                    }

                }
            }
        });
        if (!e) var e = window.e
        e.cancelBubble = true;
        if (e.stopPropagation) e.stopPropagation();
    }
    $(document).ready(function (event) {
        loadMore(event);

        // Thông báo realtime
        listenPusherMessage();
    });

    function listenPusherMessage() {
        var pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
            cluster: '{{env('PUSHER_APP_CLUSTER')}}',
            encrypted: '{{env('PUSHER_APP_ENCRYPTED')}}'
        });

        // Subscribe to the channel we specified in our Laravel Event
        var channel = pusher.subscribe('admin');

        // Bind a function to a Event (the full Laravel class)
        channel.bind('admin_notify', function(data) {
            {{--$.playSound("{{asset('admin/plugin/sound-toast/new-order.mp3')}}");--}}
            $.toast({
                heading: data.title,
                text: data.text,
                icon: 'info',
                showHideTransition: 'fade',
                allowToastClose: true,
                hideAfter: 10000,
                stack: false,
                position: 'top-right',
                textAlign: 'left',
                loader: true,
                loaderBg: '#9EC600',
            });

            // notifications.html(newNotificationHtml + existingNotifications);
            // notificationsCount += 1;
            // notificationsCountElem.attr('data-count', notificationsCount);
            // notificationsWrapper.find('.notif-count').text(notificationsCount);
            // notificationsWrapper.show();
        });

    }
</script>
