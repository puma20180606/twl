var table=new Table(48,7)

function Table(rownum,colnum)
{
	this.rownum=rownum;
	this.colnum=colnum;
	this.cellarr=[]

}
/*row is from 0
  col is from 1
*/
Table.prototype.init=function()
{
	for(var i=0;i<this.rownum;i++)
	{
		this.cellarr[i]=[]
		for(var j=1;j<=this.colnum;j++)
			this.cellarr[i][j]=false;
	}
}
Table.prototype.check=function(coordinate)
{
	var row=coordinate[0];
	var col=coordinate[1]
	if(this.cellarr[row][col]==true)
		return false;
	return true;
}
/*rowindex is from 0
 * colindex is from 1
*/
Table.prototype.occupy=function(rowindex,colindex)
{
	this.cellarr[rowindex][colindex]=true;
}
Table.prototype.release=function(rowindex,colindex)
{
	this.cellarr[rowindex][colindex]=false;
}
Table.prototype.releaseRange=function(fromrowindex,torowindex,colindex)
{
	for(var i=fromrowindex;i<torowindex;i++)
	{
		this.cellarr[i][colindex]=false;
	}
	
}
function formatTime(time)
{
	var time=time.toString();
	var index=time.indexOf('.')
	if(index==-1){
		return time+':00'
	}
	else{
		var hour=time.substring(0,index)
		var min	=time.substring(index+1)
		if(min=='5'){
			min=":30"
		}
		else if((min=='0')||(min='00')){
			min=":00"
		} 

		return hour+min;
	}
}
function reversecolor(color)
{
	if(!color)return;
	var start=color.indexOf('(');
	var end=color.indexOf(')')
	var color=color.substring(start+1,end)
	var color=color.split(',')
	var r=255-color[0].valueOf(),g=255-color[1].valueOf(),b=255-color[2].valueOf();
	color='rgb('+r+','+g+','+b+')'
	return color
}

function r_color(color)
{
	var color=color.split(',')
	var r=255-color[0].valueOf(),g=255-color[1].valueOf(),b=255-color[2].valueOf();
	color='rgb('+r+','+g+','+b+')'
	return color
}
function Worklog(options,date,fromtime,totime,data,parent,label){
	if(!options.id) return;
	this.color	=options.color;
	this.rowheight=(opera)?29:30;
	this.width	=options.width?options.width:100;
	this.height	=(totime-fromtime)*2*this.rowheight
	this.minheight=this.height;
	this.label	=label
	this.top	=(options.top)?options.top:0;
	this.left	=(options.left)?options.left:0;
	this.id		=options.id;
	this.data	=(data!=null?data:'');
	this.url	='/dailylog/index/savemylog'
	this.col	=0
	this.date	=date;
	this.fromtime=fromtime;
	this.totime=totime
	this.archive=false;
	this.html="<div class='worklog' id="+this.id+"><div  unselectable='on' class='timespan'>"+formatTime(this.fromtime)+'---'+formatTime(this.totime)+"</div><div unselectable='on' class='data'>"+this.data+"</div><div unselectable='on' class='handle'>ˇ</div></div>";
	var div=$(this.html).css({'background-color':this.color,width:this.width-4,height:this.height-2})
	$('.handle',div).css({top:this.height-10})
	if(parent==null){
		var mylog=$('#mylog')
		var row=this.fromtime*2
		var tr=$('tr',mylog).eq(this.fromtime*2)
		tr=$('#mylog tbody tr').eq(this.fromtime*2)
		var col=0;
		$('#mylog thead .date').each(function(i){
			if(date==$(this).text()){
				col=i
				return ;
			}
		})
		col=(this.fromtime*2%2==0)?col+1:col
		var td=$('td',tr).eq(col)
		parent=td
		this.col=(this.fromtime*2%2==0)?col:col+1;
		for(var i=row;i<this.totime*2;i++)
			table.occupy(i,this.col)
	}else{
		var tr=parent.parent();
		var row=$('#mylog tbody tr').index(tr);
		var col=$('td',tr).index(parent)
		this.col=(row%2==1)?col+1:col
		table.occupy(row,this.col)
	}
	parent.append(div)
	this.obj	=$("#"+this.id);
	div.data('object',this)
	if(this.label&&labelTable[this.label]){
		var color=labelTable[this.label]
		var r=color.substring(0,3),g=color.substring(3,6),b=color.substring(6,9)
		var rgb='rgb('+r+','+g+','+b+')'
		this.obj.css('background-color',rgb)
	}
}

function getrow(time)
{
	return time*2
}
Worklog.prototype.destroy=function(){
	this.obj.remove()
	delete this
	var fromrow=getrow(this.fromtime)
 	torow	=getrow(this.totime);
	table.releaseRange(fromrow,torow,this.col)
}
Worklog.prototype.save=function(content)
{
	$('.data',this.obj).empty().append(htmlspecial(content))
	this.data=content
	$('.handle',this.obj).hide()
	var data={
		'date':this.date,
		'fromtime':this.fromtime,
		'totime':this.totime,
		'label':this.label,
		'content':content
	};
	$.post(this.url,data,function(){
	
	})
}
Worklog.prototype.updatetime=function()
{
	var data={
			'date':this.date,
			'fromtime':this.fromtime,
			'totime':this.totime,
		};
		$.post('/dailylog/index/updatetime',data,function(){
		
		})
}
Worklog.prototype.remove=function(){
	
	 data={'date':this.date,'fromtime':this.fromtime,'totime':this.totime}
	 $.post('/dailylog/index/deletelog',data,function(data){
		currentlog.destroy()
	 }) 
	
}
Worklog.prototype.isEmpty=function(){
	var content=$('.data',this.obj).html()
	if(!content){
	
		return true;
	}
	return false;
}
Worklog.prototype.getcontent=function(){
//	$(this.id).html();
}
Worklog.prototype.fresh=function()
{
	if(!this.label || !labelTable[this.label]) return;
	var border=2*(this.totime-this.fromtime-0.5);
	
	this.obj.css({'height':this.height+border-4})
	$('.timespan',this.obj).empty().html(formatTime(this.fromtime)+"--"+formatTime(this.totime))
	$('.handle',this.obj).css({top:this.height+border-10})
	var color=labelTable[this.label]
	var r=color.substring(0,3),g=color.substring(3,6),b=color.substring(6,9)
	var rgb='rgb('+r+','+g+','+b+')'
	this.obj.css('background-color',rgb)
}
Worklog.prototype.setArchive=function()
{
	this.archive=true;
	this.obj.prepend('<div class="icon-lock icon-white" style="width:15px;height:15px"></div>')
}
Worklog.prototype.init=function()
{
	if(this.archive) return;
	this.obj.on('click',function(e){
		if(e.type=='click'){
			$('.handle').hide()
			$('.handle',this).show()
			var top=$(this).css('top');
			var offset=$(this).offset()
			var width=$(this).width()
			var hight=Math.floor($(this).height()/2)
			$('#minibox').offset({'left':offset.left+width+20,'top':offset.top-50}).css('visibility','visible').show()
			if(currentlog instanceof Worklog &&currentlog!=$(this).data('object')){
				if(currentlog.isEmpty())
					currentlog.destroy()
			}
			currentlog=$(this).data('object')
			var content=currentlog.data
			$('#minibox textarea').val(htmlspecial_decode(content))
		}
	})

	this.obj.on('mousedown mouseover mousemove mouseup mouseout mousein',function(e){
		switch (e.type){
			
			case	'mousedown':
				if(1==mouse_status){//if mouse is over
					mouse_status=2;//mouse is down
					oldy=e.pageY;
				}
		
				break;
			case	'mousemove':
				switch(mouse_status){
					case 2://mouse is down
						mouse_status=3;//mouse is move
						var h=e.pageY-oldy;
						oldy=e.pageY		
					//	var height=$(this).height()+h;
					//	$(this).height(height)
					//	$('.handle',this).css({top:height-10})
				
						break;
			
					
					case 3:
					break;
		/*				var h=e.pageY-oldy;
							oldy=e.pageY;
						var dir=(h>0)?1:-1;
						obj=$(this).data('object')
						var height=obj.height+h
						if(height<=obj.minheight){
							$(this).height(obj.minheight)
							$('.handle',this).css({top:height-10})
							break;
						}
						if(height%obj.rowheight>=10){
							obj.height=height=(Math.floor(height/obj.rowheight)+dir)*obj.rowheight
						}
						$(this).height(height-4)
						obj.height=height
						$('.handle',this).css({top:height-10})
						
						break;
			*/	}
		}
	})
	$('.handle',this.obj).click(function(e){e.preventDefault()})
	$('.handle',this.obj).on('mousedown',function(e){
		if(mouse_status==0)
			mouse_status=1;//mouse over
		
		
	})
//	$('.handle',this.obj).blur()
}
var mouse_status=0;//mouseup
var y,oldy;
var rowheight=26

/*
*
*Dialog
*/
function Dialog(placeholder,func,labelid,closebtn,button)
{
	this.changed=false;//trace color only
	this.text=null;
	this.numArchive=0;
	this.labelid=labelid;
	this.mergeTo=-1
	this.openmenu=false;
	this.height=null
	this.caller=null;
	this.obj=$('<div class="modal"><div class="modal-body"><div><input type=text  placeholder='+placeholder+' /></div></div><div class="modal-footer"><button class="btn" data-dismiss="modal">'+closebtn+'</button> <button class="btn btn-primary">'+button+'</button></div></div>')
	switch(func){
		case 'save':
			this.facility='savecolor';
			break;
		case 'archive':
			this.getStatistics();			
			this.facility='archive';
			break;
		case 'merge':
			$(':input[type=text]',this.obj).attr('disabled','disabled').after('<input type=text id="mergeTo" disabled>').after('Merge To')
			this.facility='merge';
			break;
		case 'Send':
			$(':input[type=text]',this.obj).after('<textarea class="span5" rows=10></textarea>')
			this.facility='sendmail';
			break;
	}
}
Dialog.prototype.setLabelTable=function(labeltable)
{
	var dlg=this
	var group=$('<div class="btn-group"><button class="btn btn-mini dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu"></ul></div>').appendTo($('.modal-body',this.obj))
	$('tr',$("#"+labeltable)).each(function(){
		var labelid	=$('td:eq(0) div',$(this)).attr('data')
		var bg		=$('td:eq(0) div',$(this)).css('background-color')
		var labelname=$('td:eq(1)',$(this)).text();
		if(dlg.labelid != labelid){
			var div=$('<div class="colorblock"></div>').css('background-color',bg)
			var li=$('<li data='+labelid+'>'+labelname+'</li>').prepend(div)
		//	var option=$('<option value='+labelid+'>'+labelname+'</option>')
			$('ul',group).append(li)
		}
	})
	$('li',group).click(function(){
		var val=$(this).attr('data')
		var color=reversecolor($('div',$(this)).css('background-color'))
		$('#mergeTo',dlg.obj).css('background-color',$('div',$(this)).css('background-color')).css('color',color).attr('data',val).val($(this).text())
		dlg.mergeTo=val
		$('.modal-body',dlg.obj).height(dlg.height)	
	})
	$('.dropdown-toggle',group).click(function(){
			var size=$('#labellist tr').size();
			dlg.height=$('.modal-body',dlg.obj).height()
			$('.modal-body',dlg.obj).height(dlg.height+size*20)		
	})
}
Dialog.prototype.setcaller=function(caller)
{
	this.caller=caller;
}
var dlg;
Dialog.prototype.init=function()
{
	dlg=this
	var obj=this.obj
	$('input[type=text]',obj).change(function(){
		dlg.text=$(this).val()
	})
	var tr=$(dlg.caller).parents('tr')
	var bg=$('.labelcolor',tr).css('background-color')
	$(':input[type=text]:first',obj).css('background-color',bg).css('color',reversecolor(bg))
	$('.btn-primary',this.obj).click(function(){
			switch(dlg.facility){
				case 'savecolor':
					if(dlg.changed || dlg.text){
						var bg=$(':input[type=text]',obj).css('background-color');
						$.post('/dailylog/label/edit',{'id':dlg.labelid,'labelname':dlg.text,'color':bg})
						var tr=$(dlg.caller).parents('tr')
						$('.labelcolor',tr).css('background-color',bg)
						if(dlg.text)$('td:eq(1)',tr).text(dlg.text)
					}
					break;
				case 'archive':
					$(':input[type=text]:first',obj).css('background-color',bg).css('color',reversecolor(bg))
					if(dlg.numArchive>0)
						$.post('/dailylog/label/archive',{'labelid':dlg.labelid})
					
					break;
				case 'merge':
					$(':input[type=text]:first',obj).css('background-color',bg).css('color',reversecolor(bg))
					if(dlg.mergeTo!=-1)
						$.post('/dailylog/label/merge',{'from':dlg.labelid,'to':dlg.mergeTo});
					
					break;
				case 'sendmail':
					if(dlg.text!=null && dlg.text!='' &&checkemailformat(dlg.text))
					{
						var content=$('textarea',obj).val()
						$.post('/team/index/invite',{'email':dlg.text,'content':content})
					}
			}
			
		dlg.close()
		
	})
}
Dialog.prototype.show=function()
{
	this.obj.modal();
}
Dialog.prototype.close=function()
{
	this.obj.modal('hide')
}
Dialog.prototype.addcolorpicker=function(pickerid)
{
	var dlg=this;
	var obj=$('.modal-body',this.obj).append("<div class='btn btn-success selectcolor' data='down'>V</div>")
	$('.selectcolor',obj).on('click',function(){
		if($(this).attr('data')=='down'){
			$(this).after($('#'+pickerid).clone())
			$(this).text('∧')
			$(this).attr('data','up')
			$('.colorpicker div',obj).click(function(){
				var bg=$(this).css('background-color')
				$(':input[type=text]',obj).css('background-color',bg)
				dlg.changed=true;
			})
			
		}else{
			$('.colorpicker',obj).remove()
			$(this).attr('data','down')
		}
			
	})
}
Dialog.prototype.getStatistics=function()
{
	var dlg=this
	$.get('/dailylog/label/archive',{'labelid':this.labelid},function(data){
		dlg.numArchive=data;
		if(dlg.numArchive){
			$('.modal-body',dlg.obj).append('<div>Total '+dlg.numArchive+' logs can be archived</div>')
		}
		else{
			$('.modal-body',dlg.obj).append('<div>No logs belong to the label</div>')		
		}
	})
}
Dialog.prototype.addlistener=function()
{
	
}
function checkemailformat(text)
{
	
	return true;
}
