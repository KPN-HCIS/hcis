<<<<<<< HEAD
function t({initialHeight:e}){return{init:function(){this.render()},render:function(){this.$el.scrollHeight>0&&(this.$el.style.height=e+"rem",this.$el.style.height=this.$el.scrollHeight+"px")}}}export{t as default};
=======
function i({initialHeight:t}){return{height:t+"rem",init:function(){this.setInitialHeight(),this.setUpResizeObserver()},setInitialHeight:function(){this.height=t+"rem",!(this.$el.scrollHeight<=0)&&(this.$el.style.height=this.height)},resize:function(){if(this.setInitialHeight(),this.$el.scrollHeight<=0)return;let e=this.$el.scrollHeight+"px";this.height!==e&&(this.height=e,this.$el.style.height=this.height)},setUpResizeObserver:function(){new ResizeObserver(()=>{this.height=this.$el.style.height}).observe(this.$el)}}}export{i as default};
>>>>>>> main
