@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="col-md-4">
                        <p>Kişi Listesi</p>
                        <xbody></xbody>
                    </div>
                    <div style="overflow-y: scroll;height:400px" id="message" class="col-md-8">
                        <p>Mesajlar</p>
                        <ybody></ybody>
                    </div>
                    <br>
                    <div class="form-group">
                        <textarea class="form-control" id="comment" name='comment' onkeyup="test()" rows="2"></textarea>
                    </div>
                    <input type="hidden" id="btnId"></input>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class="col-md-12">
                            <button id="sendMessage" onclick="sendMessage()" class="btn btn-primary">
                               Gönder
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="/socket.io/socket.io.js"></script>

<script>
    
    $(document).ready(function(){
        if(document.getElementById("comment").value.length<10){
            document.getElementById("sendMessage").disabled=true;
        }
    });
    
    function test() {
        var say = document.getElementById("comment").value.length;
        if (say > 10)
            document.getElementById("sendMessage").disabled=false;
        else
            document.getElementById("sendMessage").disabled=true;
    };
    
    function doSomething()
    {
        var time = new Date();
        var hour = time.getHours();
        var mint = time.getMinutes();
        if(mint==0){
            alert('Guk Guk Saat : '+hour);
        }
    }
    setInterval(doSomething, 60*500);
    
    
    var socketId = "";
    var socket = io.connect("http://vukhaq1f5r0xysj6.tzty.net:1703");    
    
    //socket bağlantısı var ise kullanıcıları çek
    socket.on('socketId',function(data){
        socketId = data.id;
        users();
    });
    
    //mesaj verilerini al
    socket.on('chat',function(data){
        var senderId = data.senderId;
        var receiverId = data.receiverId;
        var message = data.message;
        var gonderenAd = data.gonderenAd;
        if(receiverId=={{Auth::user()->id}}){
            alert(gonderenAd+" isimli kişiden bir mesajınız var.");
            messages(senderId);
        }
    });  
    
    //mesaj verilerini gönder
    function sendMessage(){       
        id = document.getElementById("btnId").value;
        socket.emit('chat',{
        senderId:'{{ Auth::user()->id }}',
        receiverId:id,
        message:document.getElementById("comment").value,
        gonderenAd:'{{ Auth::user()->name }}'
        });
        createData({{ Auth::user()->id }},id,document.getElementById("comment").value);
        document.getElementById("comment").value = "";
        document.getElementById('message').scrollTop = 9999999;
    }
    
    //aktif button
    function button(id){
        document.getElementById('btnId').value = id;
        document.getElementById('message').scrollTop = 9999999;
    }
    
    //kullanıcıları çek
    function users()
    {
        $.ajax({
            url:"message/users",
            dataType:"json",
            success:function(data)
            {
                var html = '';
                for(var count=0; count<data.length; count++)
                {
                    html +='<button style="width: 99%; margin: 5px" class="btn btn-primary" id="userId" value="'+data[count].id+'" onclick="messages('+data[count].id+')">'+data[count].name+'</button>';
                    html +='<br>';
                }
                $('xbody').html(html);                  
            }
        });
    }
    
    //mesajları çek
    function messages(id)
    {
        button(id);
        $.ajax({
            url:"message/messages/"+id,
            dataType:"json",
            success:function(data)
            {
                var html = '';
                for(var count=0; count<data.length; count++)
                {
                    html +='<nav aria-label="breadcrumb">';
                    html +='<ol class="breadcrumb">';
                    if({{ Auth::user()->id }} == data[count].senderId)
                    {
                        html +='<p align="right" margin="10px">';
                    }else{
                        html +='<p margin="10px">';
                    }                    
                    html +=data[count].messageText;
                    html +='</p>';
                    html +='</ol>';
                    html +='</nav>';
                }
                $('ybody').html(html);                  
            }
        });
        document.getElementById('message').scrollTop = 9999999;
    }
    
    //mesajı veritabanına yaz
    function createData(senderId,receiverId,message)
    {
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url:"{{ route('messages.createData') }}",
            method:"POST",
            data:{senderId:senderId, receiverId:receiverId, message:message, _token:_token},
            success:function(data)
            {
               messages(receiverId); 
            }
        });
    }
    
        
  
  
  
</script>

@endsection





