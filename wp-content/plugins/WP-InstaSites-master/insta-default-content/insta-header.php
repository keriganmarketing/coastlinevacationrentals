<div class="row-fluid">
	<div class="span6">
		<div class="logo">
			<a href="/"><img src="{{site.SolutionLogo}}" alt="logo"></a>			
		</div>
		<div class="tagline">{{site.SolutionTagline}}</div>
	</div>
	<div class="span6">
		<div class="primaryphone">{{site.PrimaryPhone}}</div>
		<div class="primaryemail">{{site.PrimaryEmail}}</div>
		<div class="currencyselector"></div>
		<div class="siteselector">
			{{#site.HasMultiSites}}
				{{#site.Sites}}
			<a href='http://{{Url}}'><img src="*blank.gif" class="flag flag-{{Language}}" alt="{{RegionInfo.DisplayName}}" /><span style="display:none">{{RegionInfo.DisplayName}}</span></a>
				{{/site.Sites}}
			{{/site.HasMultiSites}}
		</div>
		<div class="social-media">
			<a href="/"><img src="http://instaville.lodgingcloud.com/wp-content/uploads/sites/3/2013/03/icon-youtube.png" alt="icon-youtube" ></a>
			<a href="/"><img src="http://instaville.lodgingcloud.com/wp-content/uploads/sites/3/2013/03/icon-gplus.png" alt="icon-gplus" ></a>
			<a href="/"><img src="http://instaville.lodgingcloud.com/wp-content/uploads/sites/3/2013/03/icon-skype.png" alt="icon-skype" ></a>
			<a href="/"><img src="http://instaville.lodgingcloud.com/wp-content/uploads/sites/3/2013/03/icon-tw.png" alt="icon-tw" ></a>
			<a href="/"><img src="http://instaville.lodgingcloud.com/wp-content/uploads/sites/3/2013/03/icon-fb.png" alt="icon-fb" ></a>
		</div>
	</div>
</div>
