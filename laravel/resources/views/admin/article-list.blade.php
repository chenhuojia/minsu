@include('public.header')
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a href='welcome'>首页</a> <span class="c-gray en">&gt;</span><a href='order'>订单管理</a> <span class="c-gray en">&gt;</span>订单列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
	<div class="text-c">
		<button onclick="removeIframe()" class="btn btn-primary radius">关闭选项卡</button>
	 <span class="select-box inline">
		<input type="text" name="" id="" placeholder="收件人名称" style="width:250px" class="input-text">
		<button name="" id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i> 搜订单</button>
	</div>	
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort table-responsive">
			<thead>
				<tr class="text-c">
					<th width="25"><input type="checkbox" name="" value=""></th>
					<th width="40">ID</th>
					<th>项目</th>
					<th>收件人</th>
					<th>收件国家</th>
					<th>数量</th>
					<th>订单总价</th>
					<th>优惠</th>
					<th>实付款订单金额</th>
					<th>订单来源站点</th>
					<th width="120">状态</th>
					<th width="120">操作</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($orders as $v)
    				<tr class="text-c">
    					<td><input type="checkbox" value="" name=""></td>
    					<td>{{$v->order_id}}</td>
    					<td class="text-l">
    						(
                            @foreach ($v->team as $team)
                               {{$team->title}} &nbsp;
                            @endforeach
                            )
    					</td>
    					<td>{{$v->consignee_name.'&nbsp;'.$v->consignee_phone}}</td>
    					<td>{{$v->consignee_country}}</td>
    					<td>{{$v->team_num}}</td>
    					<td>{{$v->order_price}}</td>
    					<td>{{$v->discount_price}}</td>
    					<td>{{$v->order_amount}}</td>
    					<td>{{$v->order_from}}</td>			
    					<td class="td-status">
    						@if ($v->order_state === 1)
    							<span class="label label-success radius">已处理</span>
    						@elseif ($v->order_state==0)
    							<span class="label label-fail radius">未处理</span>
    						@endif
    					</td>
    					<td class="f-14 td-manage">
    						<a style="text-decoration:none" class="ml-5" onClick="article_edit('订单详情','view/{{$v->order_id}}','10001')" href="javascript:;" title="编辑"><i class="Hui-iconfont">&#xe6df;</i></a> 
    					</td>
    				</tr>
				@endforeach				
			</tbody>
		</table>
	</div>
	{{ $orders->links() }}
</div>
<!--_footer 作为公共模版分离出去-->
@include('public.javascript')
<script type="text/javascript">
/*资讯-编辑*/
function article_edit(title,url,id,w,h){
	var index = layer.open({
		type: 2,
		title: title,
		content: url
	});
	layer.full(index);
}
</script> 
</body>
</html>