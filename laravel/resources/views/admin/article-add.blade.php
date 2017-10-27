@include('public.header')
<article class="page-container">
	<form class="form form-horizontal" id="form-article-add">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">收件人：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="{{$order->consignee_name}}" placeholder="" id="articletitle" name="articletitle">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">收件人手机号码：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="{{$order->consignee_phone}}" placeholder="" id="sources" name="sources">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">收件地址：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="{{$order->consignee_country.$order->consignee_province.$order->consignee_city.$order->consignee_area.$order->consignee_address}}" placeholder="" id="author" name="author">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">收件人邮箱：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="{{$order->consignee_zipcode}}" placeholder="" id="articletitle2" name="articletitle2">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">发件人：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="{{$order->sender_name}}" placeholder="" id="author" name="author">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">发件人手机号码：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="{{$order->sender_phone}}" placeholder="" id="sources" name="sources">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">发件地址：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="{{$order->sender_country.$order->sender_province.$order->sender_city.$order->sender_area.$order->sender_address}}" placeholder="" id="author" name="author">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">发件人邮箱：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="{{$order->sender_zipcode}}" placeholder="" id="sources" name="sources">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>项目名称：</label>
			<div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
				<input type="text" disabled class="input-text" value="{{$order->order_team}}" placeholder="" id="sources" name="sources">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>购买数量：</label>
			<div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
				<input type="text" disabled class="input-text" value="{{$order->team_num}}" placeholder="" id="sources" name="sources">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">付款状态：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="已支付" placeholder="" id="sources" name="sources">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">订单状态：</label>
			<div class="formControls col-xs-8 col-sm-9">
				@if($order->order_state == 0)
					<input type="text" disabled class="input-text" value="未处理" placeholder="" id="keywords" name="keywords">
				@elseif($order->order_state == 1)
					<input type="text" disabled class="input-text" value="已处理" placeholder="" id="keywords" name="keywords">
				@endif
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">交易单号：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="{{$order->pay_id}}" placeholder="" id="author" name="author">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">支付金额：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="{{$order->pay_price}}" placeholder="" id="sources" name="sources">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">订购时间：</label>
			<div class="formControls col-xs-8 col-sm-9 skin-minimal">
				<input type="text" disabled class="input-text" value="{{$order->addtime}}" placeholder="" id="sources" name="sources">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">订单来源：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" disabled class="input-text" value="{{$order->order_from}}" placeholder="" id="sources" name="sources">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">订单留言：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<textarea name="abstract" disabled cols="" rows="" class="textarea"  placeholder="说点什么...最少输入10个字符" datatype="*10-100" dragonfly="true" nullmsg="备注不能为空！">{{$order->user_remark}}</textarea>	
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">订单备注：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<textarea name="abstract"  cols="" rows="" class="textarea"  placeholder="说点什么...最少输入10个字符" datatype="*10-100" dragonfly="true" nullmsg="备注不能为空！">{{$order->admin_remark}}</textarea>	
			</div>
		</div>	
	</form>
</article>
@include('public.footer')