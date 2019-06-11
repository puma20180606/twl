var imagefilefield='';
var serverUploadfile='';
var imageWidth=0,imageHeight=0;
var labelTable=[];
function deletelabel(tr,labelid)
{
	$.post('/dailylog/label/del',{'id':labelid},function(data){		
		if(data.result=='success'){
			tr.remove();
			if($('.labelcolor').size()==1){
				$('.delete').hide()
				$('.archive').hide()
				$('.merge').hide()
			}
		}
	})
}


function showcolorlist()
{
	$('#colorbtn').click(function(){
		var parent=$(this).siblings('ul')
		$('.colorpicker',parent).show()
	})
}
//the param obj is the tr jquery object 
function getRowCol(totime,col)
{
	var row=totime*2
	var coordinate=[row,col];	
	return coordinate;
}
var currentlog;
var opera
(function(){
//	window.getSelection().removeAllRanges(); 
//	opera=($.browser.opera)?true:false
	showcolorlist()
	table.init()
	$('td,th,div').bind('selectstart',function(){ return false});
	$('#mylog td').click(function(e){
		if(e.target!=this) return ;
		var width=$(this).width();
		var height=$(this).height();
		var offset=$(this).offset();
		var top	=offset.top;
		var left=offset.left;

		var color='#f00';
		var tdindex=$(this).index()
		
		var trindex=$(this).parent().index()
		if(trindex%2==0&&tdindex==0){return}
		if(trindex%2==1){
			tdindex+=1;
		}
		if(!table.check([trindex,tdindex]))return;
		if(currentlog instanceof Worklog){
			if(currentlog.isEmpty())
			{
				currentlog.destroy()
			}
		}
		var date=$('.date').eq(tdindex-1).text()
		var fromtime=trindex/2
		var totime=fromtime+0.5
		var label=$('#labelsel').attr('data');
		currentlog=new Worklog({'color':'#006699','id':'log'+trindex+'_'+tdindex,top:top,left:left,width:width,height:height},date,fromtime,totime,'',$(this),label)
		currentlog.init()
		currentlog.fresh()
		$('#minibox textarea').val('')
		$('#minibox').show();
		$('#minibox').offset({top:top-50,left:left+width+20}).css('visibility','visible');		
	})

	$('#mylog td').on('mousemove mouseup',function(e){
		if(mouse_status==3){
			if(e.type=='mousemove')
			{
				var offset=$(this).offset();
				var top=offset.top;
				if(e.pageY>oldy&&(e.pageY-top)>$(this).height()/2&&e.pageY<currentlog.top+currentlog.height+$(this).height()&&e.pageY>currentlog.top+currentlog.height){
						var coordinate=getRowCol(currentlog.totime,currentlog.col)
						if(!table.check(coordinate))return;
						currentlog.height=currentlog.height+$(this).height();
						currentlog.totime=currentlog.totime-0
						currentlog.totime+=0.5;		
						table.occupy(coordinate[0], coordinate[1])
						currentlog.fresh();
						oldy=e.pageY;
				}else if(e.pageY<oldy&&currentlog.height>$(this).height()&&e.pageY<currentlog.top+currentlog.height&&e.pageY>currentlog.top+currentlog.height-$(this).height()){
					currentlog.totime-=0.5
					if(currentlog.totime<=currentlog.fromtime){
						currentlog.totime=currentlog.fromtime+0.5
						return;
					}
					currentlog.height=currentlog.height-$(this).height();
					var coordinate=getRowCol(currentlog.totime,currentlog.col)
					table.release(coordinate[0], coordinate[1])
					currentlog.fresh()	;
					oldy=e.pageY
				}
			}
			if(e.type=='mouseup'){
				mouse_status=0
				currentlog.updatetime();
			}
		}
		
	})	
	$('#mylog tr:even td').hover(
			function(){$(this).addClass('hover')},
			function(){$(this).removeClass('hover')}
	)
	$('#mylog tr:odd td:not(:first-child)').hover(
			function(){$(this).addClass('hover')},
			function(){$(this).removeClass('hover')}
	)
	
	$('#mylog tr:odd td:first-child').mousedown(function(){$(this).blur()})
	$('#boxsave').on('click',function(){
		var textarea=$(this).parent('#minibox').children('textarea');
		var content =textarea.val();
		currentlog.label=$('#labelsel').attr('data')
		currentlog.fresh()
		currentlog.save(content)
		$('#minibox').css('visibility','visible')
		textarea.val('')
		$(this).attr('disabled','disabled')
		
	})
	
	$('#boxcancel').click(function(){
		$('#minibox').offset({top:0,left:0}).css('visibility','hidden')
		if(currentlog.data==''){
			currentlog.destroy()
		}
	})
	$('#boxdelete').click(function(){
		if(confirm('确定要删除该日志吗？')){
			if(currentlog instanceof Worklog){
				currentlog.remove();
				$('#minibox').offset({left:0,top:0}).css('visibility','hidden')
			}	
		}	
	})
/*	$('#preweek').click(function(e){
		e.preventDefault()
		var href=$(this).attr('href');
		var arr=href.split("?");
		var date=arr[1];
		$('#weeknav input').val(date)
		$.get('/dailylog/index/preweek',{'date':date},function(){
					
		});
	})
*/
	$('#nextweek').click(function(e){
		e.preventDefault()
		weekNav(this);
	})
	$('#preweek').click(function(e){
		e.preventDefault()
		weekNav(this);
	})
	showMylog(this)
	//$('#navbar li a').click(function(e){e.preventDefault();$(this).blur()})
	$('.nav-tabs li').click(function(){
		$('#navbar li').removeClass('active');
		$(this).addClass('active')	

	})
	$('#setting li a').click(function(e){e.preventDefault()})
	$('#setting li').click(function(){$('#setting li').removeClass('active');$(this).addClass('active')
			i=$(this).index()+1
			id='#set'+i;
			$('#setblock>div').hide();
			$(id).show();
	})
				
	$('#uploadphoto').click(function(){
		$('#portrait').click()
	})

	$('#portrait').change(function(){
		uploadfile()
	})
	$('#upload').click(function(){
		uploadfile('portrait')
	
	})
	$('#profilesave').click(function(e){
		e.preventDefault();
		var nickname=$('#nickname').val();
		$.post('/user/index/saveprofile',{'portrait':$('#imagefile').val(),'nickname':nickname,'phone':$('#phone').val()},function(data){
			if(data='ok'){
			$('#setblock .modal-header').text(nickname);
			$('#nickname').val('').attr('placeholder',nickname)
			$('#set1 .result').empty().append('更改成功')
			}
		})
	})
	$('#emailset').click(function(){
		if($('#email').val()==''){
			$.get('/user/index/getemail',function(data){
				$('#email').val(data)
				
			})
		}
	})
	$('#changeemail').click(function(e){
		e.preventDefault();
		var validator=/^[a-zA-Z0-9_+.-]+\@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]{2,4}$/;
		if(!validator.test($('#newemail').val())){
			$('#set2 .result').html('邮箱地址格式不正确')
			return ;
		}
		$.post('/user/index/changemail',{'newemail':$('#newemail').val(),'confirmemail':$('#confirmemail').val()},function(data){
			if(data.code==-1){
			//	$('#reason').text(data.reason)
				$('#set2 .result').empty().append(data.reason)
				return;
			}else if(data.code==1) $('#set2 .result').empty().append('更改成功')
			$('#email').val($('#newemail').val());
			$('#newemail').val('')
			$('#confirmemail').val('')
		})
	})
	$('#changepw').click(function(e){
		e.preventDefault();
		var password=$('#password').val();
		var newpw	=$('#newpassword').val();
		var confirmpw=$('#confirmpw').val();
		$.post('/user/index/changepw',{'password':password,'newpassword':newpw,'confirmpw':confirmpw},function(data){
			if(data['code']==-1){
				$('#set3 .result').empty().append(data['reason']);

			}else{
				if(data['code']==1)$('#set3 .result').empty().append('更改成功');
			}
		})
	})
	$('.colorpicker td div').click(function(){
		var bg=$(this).css('background-color')
		$('#label').css('background-color',bg)
	})
	$('#addlabel').click(function(){
		if($('.labelcolor').size()==1){
			$('.delete').show()
			$('.archive').show()
			$('.merge').show()
		}
		var label=$('#label').val()
		
		if(label){
			var data={'name':label,'color':$('#label').css('background-color')}
			$.post('/dailylog/label/add',data,function(data){
				var div=$("<div class='labelcolor' data="+data.id+"></div>").css('background-color',$('#label').css('background-color'))
				var td1=$("<td></td>").append(div)
				var td2=$("<td class='span2'>"+label+"</td>")
				var td3=$("<td class='span1'><a class='edit'>&nbsp;Edit</a></td>")
				var td4=$("<td class='span1'><a class='delete'>&nbsp;Del</a></td>")
				var td5=$("<td class='span1'><a class='archive'>&nbsp;Archive</a></td>")
				var td6=$("<td class='span1'><a class='merge'>Merge</a></td>")
				var tr=$('<tr></tr>').append(td1).append(td2).append(td3).append(td4).append(td5).append(td6)
				$('#labellist').append(tr)
				$('.delete',tr).on('click',function(){
					var labelid=$('.labelcolor',tr).attr('data')
					$.post('/dailylog/label/del',{'id':labelid},function(data){
						if(data.result=='success'){
							tr.remove();
							if($('.labelcolor').size()==1){
								$('.delete').hide()
								$('.archive').hide()
								$('.merge').hide()
							}
						}
					})
				})
				$('.edit',tr).on('click',function(){
					var tr=$(this).parents('tr')
					var text=$('td',tr).eq(1).text();
					var labelid=$('.labelcolor',tr).attr('data')
					var dlg=new Dialog(text,'save',labelid,'Close','Edit')
					dlg.setcaller(this)
					dlg.init()
					dlg.addcolorpicker('colorpicker')
					dlg.show()
					
				})
				$('.archive',tr).on('click',function(){
					var tr=$(this).parents('tr')
					var text=$('td',tr).eq(1).text();	
					var dlg=new Dialog(text,'存档')
					dlg.addcolorpicker('colorpicker')
					dlg.show()
					
				})
				$('.merge',tr).on('click',function(){
					var tr=$(this).parents('tr')
					var text=$('td',tr).eq(1).text();	
					var dlg=new Dialog(text,'合并')
					dlg.addcolorpicker('colorpicker')
					dlg.show()
					
				})
			})
		}	
	})
	$('#teamsetsubmit').click(function(e){
		e.preventDefault()
		var teamname=$('#teamname').val();
		var teamlogo=$('#saveteamlogo').val()
		$.post('/team/index/setting',{'teamname':teamname,'teamlogo':teamlogo},function(data){
			if(data=='success'){
				var k=$('#teamimg').attr('src');
				$('#currentteam').html(teamname).prepend('<img style="width:50px;max-height:50px" src='+k+'>')
			}
		})
	})
	
	$('#minibox textarea').bind('keyup mouseup',function(){
		if($(this).val()==''){
			$('#boxsave').attr('disabled','disable')
		}else{
			$('#boxsave').removeAttr('disabled')
		}
	})
})
function fetchlog(url,date){
	$.get(url,{'date':date},function(data){
		$('.worklog').each(function(){
			var obj=$(this).data('object');
			obj.destroy()
		})
		var href=$('#preweek').attr('href').split("?")
		var prehref=href[0]+'?'+data['week'][0]
		$('#preweek').attr('href',prehref)
		var nexthref=href[0]+'?'+data['week'][8]
		$('#nextweek').attr('href',nexthref)
		$('#mylog .date').each(function(i){
			$(this).text(data['week'][i+1])
			
		})
		showlog(data['data'],data['week'])
	});
}
function weekNav(obj)
{
	var href=$(obj).attr('href');
	var arr=href.split("?");
	var date=arr[1];
	$('.weeknav input').val(date)
	fetchlog(arr[0],date);
}
function showMylog()
{
	var fromdate,todate,weekdate=[];
	datelist=$('.date')
	fromdate=datelist.eq(0).text();
	for(var i=1;i<=7;i++)
	{
		weekdate[i]=datelist.eq(i-1).text();
	}
	todate=datelist.eq(6).text()
	
	$.get('/dailylog/index/loadlog',{fromdate: fromdate, todate: todate }, function(data){
		showlog(data,weekdate)
		$('.worklog .handle').hide()
	})
	
}
//click the preweek or nextweek ,this func will be called
function showlog(data,weekdate){	
	var date,timespan;
	for(var i in data){
		for(var j=1;j<=7;j++){
			if(data[i].date==weekdate[j])break;
		}
		//j is column number
		var fromtime=data[i].fromtime
		var row=fromtime*2;
		var totime=data[i].totime
		
		
		var offset,top,left;
		var tr=$('#mylog tbody tr').eq(row)
		col=j-((row%2==0)?0:1)
		var td=$('td',tr).eq(col)
		
		offset=td.offset();
		var log=new Worklog({'color':'#006699','id':'log'+row+'_'+j,top:offset.top,left:offset.left,width:td.width(),height:td.height()},data[i].date,fromtime,totime,data[i].content,null,data[i].label)
		if(data[i].archive==1){
			log.setArchive()
		}
		log.init()
		log.fresh()
	}
	
}
function closedlg(obj)
{
	var dlg=$(obj).parent('div').parent('div').get(0);
}

/*function uploadfile(){
	if(!imagefilefield)imagefilefield='mce_58-inp';
	filename=document.getElementById(imagefilefield).value;
	if(''==filename)return;
	var filename=document.getElementById(imagefilefield).value
	
	$('#form_upload').trigger('submit');
		
}
*/
function uploadfile(){
	$('#kkk').ajaxSubmit({
		url:'/user/index/changeportrait',
		dataType:"json",
		enctype: 'multipart/form-data',
		type:'POST',
		success:function(response,statusText,xhr,$form){
			$('#portraitimg').attr('src',response['filename']);
			$('#imagefile').val(response['filename']);
		}
	})
}
