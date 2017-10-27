@include('public.header')
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a href='welcome'>首页</a> <span class="c-gray en">&gt;</span><a href='order'>订单管理</a> <span class="c-gray en">&gt;</span>订单列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
	<div class="text-c">
    	<form action="/addother" id="postage" method="post">
    		{{ csrf_field() }}
    		<input type="hidden" id="postage_data" name="postage_data" value="" />
    		<select name="company_id" id='company_id'>
    		    @foreach($data as $v)
    				<option value='{{$v->id}}'>{{$v->name}}</option>
    			@endforeach
    		</select>
    		<input type="text" id="express_id" name="express_id" value="" />
    		<button id='submit'  class="btn btn-primary radius">打包</button>
    	</form>
		
		<button onclick="removeIframe()" class="btn btn-primary radius">关闭选项卡</button>
	 	<select id='change_web'>
	 	   @foreach($webdata as $v)
	 	   		@if($web_id == $v->id)
	 	   			<option value='{{$v->id}}' selected='selected'>{{$v->domain}}</option>
	 	   		@else
	 	   			<option value='{{$v->id}}'>{{$v->domain}}</option>
	 	   		@endif
    	   @endforeach	
	 	</select>
	 	<span class="select-box inline">
		<input type="text" name="" id="" placeholder="收件人名称" style="width:250px" class="input-text">
		<button name="" id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i> 搜订单</button>
	</div>	
	
	<div class="mt-20" >
		<table id='list' class="table table-border table-bordered table-bg table-hover table-sort table-responsive">
			<thead>
				<tr class="text-c">
					<th width="25"><input type="checkbox" name="" value=""></th>
					<th>发件人</th>
					<th>发件国家</th>
					<th>发件人地址</th>
					<th>收件人</th>
					<th>收件国家</th>
					<th>收件人地址</th>
					<th>订单ID</th>
					<th>宝贝数</th>
					<th>商品名称</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($teams as $v)
    				<tr class="text-c">
    					<td>
    						<input type="checkbox" class='checks' value="{{$v->ut}}" state=0 name="select" onclick="checkedid(this)">
    						<input type="hidden"  value="{{$v->comm}}" >
    					</td>
    					<td>{{$v->sender_name.$v->sender_phone}}</td>
    					<td class="text-l">{{$v->sender_country}}</td>
    					<td>{{$v->sender}}</td>
    					<td>{{$v->consignee_name.$v->consignee_phone}}</td>
    					<td class="text-l">{{$v->consignee_country}}</td>
    					<td>{{$v->consignee}}</td>
    					<td>{{$v->order_id}}</td>
    					<td>1</td>
    					<td>{{$v->title}}</td>			   					
    				</tr>
				@endforeach				
			</tbody>
		</table>
	</div>
	{{ $teams->links() }}
</div>
<!--_footer 作为公共模版分离出去-->
@include('public.javascript')
<script type="text/javascript">
$(function(){
	var valArr = new Array;
	$("#submit").click(function(){
		valArr = new Array;
		//console.log($("#list :input[name='select']:checked"));
		$("#list :input[name='select']:checked").each(function(i){
			if(i==0){
       		 	 address=$(this).next().val();
           	}else{
           		 address1=$(this).next().val();
          	  	 if((address != address1)){           			
            			alert('不能选择不同收件人的地址或发件人地址进行打包');
            		return false;
       		 	}
           	}
			valArr[i] = $(this).val();
		});
		if(JSON.stringify(valArr) == "[]"){
        	alert('还没有选择打包');
        	return false;
        }
		var company_id=$('#company_id option:selected').val();
		var express_id=$('#express_id').val();
		if(company_id.length <= 0){
			alert('请选择快递公司');
        	return false;
		}
		if(express_id.length <= 0){
			alert('快递单号不能为空');
        	return false;
		}
		var vals = valArr.join('||');
		$('#postage_data').val(vals);
		$('#postage').submit();
	});
});
function checkedid(obj){
	 var state=$(obj).attr('state');
	 if(state==0){
		 var sender=$(obj).next().val();
		 var inputs=$(obj).parent().parent().siblings('tr').find('input:checked');
		 $.each(inputs,function(i,v){
			 var sender1=$(v).next().val();
			 if((sender != sender1)){
				 $(obj).removeAttr('checked');
				 alert('不能选择不同收件人的地址或发件人地址进行打包');
				 return false;
			 }
		 });
		 $(obj).attr('state',1);
     }else{
    	 $(obj).attr('state',0);
     } 
}
$('#change_web').change(function(){
	var val=$(this).val();
	window.location.href='other?web='+val;
})

</script> 
</body>
</html>