<template>
	<div>
		<v-container fluid class="pt-0 grid-list-xl">
			<section-tooltip :title="$t('message.overview')" :tooltip="$t('message.dashboardOverview')"></section-tooltip>
			<!-- Dash Cards -->
			<v-row class="border-rad-sm overflow-hidden">
				<stats-card-v2
					colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12"
					:heading="$t('message.totalEarnings')"
					:amount="2145"
				>
				<div class="pa-5">
					<line-chart-shadow
						:dataSet="totalEarnings"
						:lineTension="0.4"
						:dataLabels="labels"
						:width="370"
						:height="80"
						:borderWidth=3
						:enableGradient='false'
						:enableShadow='false'
						:borderColor="ChartConfig.color.primary">
					</line-chart-shadow>
				</div>
				</stats-card-v2>
				<stats-card-v2
					colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12"
					:heading="$t('message.newCustomers')"
					:amount="136"
				>
				<div class="pa-5">
					<line-chart-shadow
						:dataSet="newCustomers"
						:lineTension="0.4"
						:dataLabels="labels"
						:width="370"
						:height="80"
						:borderWidth=3
						:enableGradient='false'
						:enableShadow='false'
						:borderColor="ChartConfig.color.info"
						>
					</line-chart-shadow>
				</div>
				</stats-card-v2>
				<stats-card-v2
					colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"
					:heading="$t('message.onlineRevenue')"
					:amount="2145"
				>
				<div class="pa-5">
					<line-chart-shadow
						:dataSet="onlineRevenue"
						:lineTension="0.4"
						:dataLabels="labels"
						:width="370"
						:height="80"
						:borderWidth=3
						:enableGradient='false'
						:enableShadow='false'
						:borderColor="ChartConfig.color.warning">
					</line-chart-shadow>
				</div>
				</stats-card-v2>
			</v-row>
			<!-- Sales -->
			<v-row>
				<app-card
					:heading="$t('message.sales')"
					colClasses="col-xl-8 col-lg-7 col-md-7 col-sm-6 col-12"
					customClasses="mb-0 sales-widget"
					:fullScreen="true"
					:reloadable="true"
					:closeable="true"
				>
					<sales class="mb-5" :height="330"></sales>	
					<v-row class="cart-wrap hidden-only pl-6">
						<v-col cols="4" class="d-custom-flex">
							<span class="mr-2"><i class="zmdi zmdi-shopping-cart-plus primary--text"></i></span>
							<p class="mb-0">
								<span class="d-block fs-14 fw-bold">2382</span>
								<span class="d-block fs-12 grey--text fw-normal">Sales</span>
							</p>
						</v-col>
						<v-col cols="4" class="d-custom-flex">
							<span class="mr-2"><i class="zmdi zmdi-eye success--text"></i></span>
							<p class="mb-0">
								<span class="d-block fs-14 fw-bold">53,255</span>
								<span class="d-block fs-12 grey--text fw-normal">Views</span>
							</p>
						</v-col>
						<v-col cols="4" class="d-custom-flex">
							<span class="mr-2"><i class="zmdi zmdi-equalizer error--text"></i></span>
							<p class="mb-0">
								<span class="d-block fs-14 fw-bold">$1,25,000</span>
								<span class="d-block fs-12 grey--text fw-normal">Earned</span>
							</p>
						</v-col>
					</v-row>				
				</app-card>
				<!-- Devices Share -->
				<app-card
					colClasses="col-xl-4 col-lg-5 col-md-5 col-sm-6 col-12"
					:heading="$t('message.devicesShare')"
					:fullScreen="true"
					:reloadable="true"
					:closeable="true"
					customClasses="device-share-widget"
				>
					<device-share></device-share>
				</app-card>
			</v-row>
			<v-row>
				<!-- Recent Sale -->
				<app-card
					:heading="$t('message.recentSale')"
					colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12"
					customClasses="mb-0"
					:fullBlock="true"
					:fullScreen="true"
					:reloadable="true"
					:closeable="true"
					:footer="true"
				>
					<recent-sale></recent-sale>
					<v-btn slot="footer" color="primary" small>{{$t('message.viewAll')}}</v-btn>
				</app-card>
				<!-- Support Request -->
				<app-card
					:heading="$t('message.supportRequest')"
					colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12"
					customClasses="mb-0"
					:fullScreen="true"
					:reloadable="true"
					:closeable="true"
					:footer="true"
					:fullBlock="true"
				>
					<support-request></support-request>
					<v-btn slot="footer" color="primary" small>{{$t('message.viewAll')}}</v-btn>
				</app-card>
				<!-- To Do list -->
				<app-card 
					colClasses="col-lg-4 col-md-4  col-sm-12 col-12" 
					:heading="$t('message.todoList')" 
					:fullScreen="true" 
					:reloadable="true" 
					:closeable="true" 
					:fullBlock="true">
					<to-do-list></to-do-list>
				</app-card>
			</v-row>
			<v-row>
				<!-- Customer Overview -->
				<app-card
					:heading="$t('message.customerOverview')"
					colClasses="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12"
					customClasses="mb-0 customer-overview-widget"
					contentCustomClass="mb-0"
					:fullScreen="true"
					:reloadable="true"
					:closeable="true"
					:fullBlock="true"
				>
					<v-row class="cart-wrap ma-0">
						<v-col hidden-xs-only sm="4">
							<div class="pa-2">
								<h3 class="mb-0">35000</h3>
								<p class="mb-0 fs-12 grey--text fw-normal">New Customers</p>
							</div>
						</v-col>
						<v-col hidden-xs-only sm="4">
							<div class="pa-2">
								<h3 class="mb-0">25000</h3>
								<p class="mb-0 fs-12 grey--text fw-normal">Returning Customers</p>
							</div>
						</v-col>
					</v-row>
					<line-chart-with-area 
						:dataLabels= "['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul' ]"
						:dataSet= "[ 90, 140, 50, 120, 160, 90, 90 ]"
						:color="ChartConfig.color.info"
						:height="170"
						:enableXAxesLine="false"
					></line-chart-with-area>
				</app-card>
				<!-- Invoices -->
				<app-card
					:heading="$t('message.invoices')"
					colClasses="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12"
					customClasses="mb-0"
					:fullScreen="true"
					:reloadable="true"
					:closeable="true"
					:fullBlock="true"
					:footer="true"
				>
					<invoice></invoice>
					<v-btn slot="footer" color="primary" small>{{$t('message.viewAll')}}</v-btn>
				</app-card>
			</v-row>
			<v-row>
				<!-- Shopping Cart -->
				<app-card
					colClasses="col-xl-8 col-lg-8 col-md-7 col-sm-12 col-12"
					:heading="$t('message.recentOrders')"
					customClasses="mb-0"
					:fullBlock="true"
					:fullScreen="true"
					:reloadable="true"
					:closeable="true"
					:footer="true"
				>
					<recent-orders></recent-orders>
					<div slot="footer" class="justify-space-between footer-flex">
						<v-btn color="primary" small>{{$t('message.viewAll')}}</v-btn>
						<v-spacer></v-spacer>
						<span class="grey--text d-custom-flex align-items-center">
							<i class="zmdi zmdi-replay mr-2"></i>
							<span class="fs-12 fw-normal">{{$t('message.updated10MinAgo')}}</span>
						</span>
					</div>					
				</app-card>	
				<v-col xl="4" md="5" lg="4" sm="12" cols="12">					
					<!-- Weekly Sales -->
					<v-row>
						<app-card
							colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-6 col-12"
							:heading="$t('message.weeklySales')"
							:fullBlock="true"
							:fullScreen="true"
							:reloadable="true"
							:closeable="true"
							customClasses="weekly-sales-widegt"
						>
							<div class="pl-4">
								<h5 class="mb-0 fw-bold">2578</h5>
								<p class="fs-12 grey--text fw-normal">+35% from last week</p>
							</div>
							<weekly-sales
								:enableXAxesLine="false"
							>
							</weekly-sales>
						</app-card>
						<!-- Reviews -->
						<v-col xl="12" lg="12" md="12" sm="6" cols="12">
							<reviews></reviews>
						</v-col>
					</v-row>
				</v-col>
			</v-row>
			<!-- Social Feeds -->
			<v-row>
				<app-card
					colClasses="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12"
					customClasses="social-share-wrap"
				>	
					<social-feeds 
						socialIcon="zmdi zmdi-facebook"
						friends="89K"
						feeds="459"
						type="facebook"
					>
					</social-feeds>
				</app-card>					
				<app-card
					colClasses="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12"
					customClasses="social-share-wrap"
				>	
					<social-feeds 
						socialIcon="zmdi zmdi-twitter"
						friends="65K"
						feeds="350"
						type="twitter"
					>
					</social-feeds>
				</app-card>
				<app-card
					colClasses="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12"
					customClasses="social-share-wrap"
				>	
					<social-feeds 
						socialIcon="zmdi zmdi-linkedin"
						friends="35K"
						feeds="689"
						type="linkedin"
					>
					</social-feeds>
				</app-card>
				<app-card
					colClasses="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12"
					customClasses="social-share-wrap"
				>	
					<social-feeds 
						socialIcon="zmdi zmdi-google"
						friends="90K"
						feeds="1000"
						type="google"
					>
					</social-feeds>
				</app-card>
			</v-row>
			<v-row>
				<!-- Top Selling -->
				<v-col xl="4" lg="4" md="5" sm="6" cols="12">
					<top-selling></top-selling>
				</v-col>
				<!-- Category Sales -->
				<app-card
					:heading="$t('message.categorySales')"
					colClasses="col-xl-8 col-lg-8 col-md-7 col-sm-6 col-12"
					customClasses="mb-0"
					contentCustomClass="pt-0"
					:fullScreen="true"
					:reloadable="true"
					:closeable="true"
				>
					<category-sale></category-sale>
					<div class="footer-flex">
						<v-spacer></v-spacer>
						<span class="grey--text d-custom-flex align-items-center">
							<i class="zmdi zmdi-replay mr-2"></i>
							<span class="fs-12 fw-normal">{{$t('message.updated10MinAgo')}}</span>
						</span>
					</div>		
				</app-card>
			</v-row>
			<v-row>
				<!-- Blog Card -->
				<app-card
					colClasses="col-xl-4 col-lg-4 col-md-4 col-12 col-sm-6"
				>
					<blog-layout-one :data="blog"></blog-layout-one>
				</app-card>
				<v-col cols="12" sm="6" md="4" lg="4" class="flex">
					<blog-layout-four></blog-layout-four>
				</v-col>	
				<!-- Add New Blog -->
				<app-card 
					colClasses="col-12 col-sm-12 col-md-4 col-lg-4"
					:heading="$t('message.newPost')"
					:fullScreen="true" 
					:reloadable="true" 
					:closeable="true"
					customClasses="blog-widget"
				>
					<new-post></new-post>
				</app-card>
			</v-row>
		</v-container>
	</div>
</template>

<script>
// charts component
import LineChartShadow from "Components/Charts/LineChartShadow";
import Sales from "Components/Charts/SalesChartV2";
import LineChartWithArea from "Components/Charts/LineChartWithArea";

// // widgets
import RecentSale from "Components/Widgets/RecentSales";
import SupportRequest from "Components/Widgets/SupportRequestV2";
import ToDoList from "Components/Widgets/ToDoList";
import Invoice from "Components/Widgets/Invoice";
import RecentOrders from "Components/Widgets/RecentOrders";
import WeeklySales from "Components/Widgets/WeeklySales.vue";
import Reviews from "Components/Widgets/Reviews";
import SocialFeeds from "Components/Widgets/SocialFeeds";
import TopSelling from "Components/Widgets/TopSelling";
import NewPost from "Components/Widgets/AddNewBlog";
import BlogLayoutOne from "Components/Widgets/BlogLayoutOne";
import BlogLayoutFour from "Components/Widgets/BlogLayoutFour";
import CategorySale from "Components/Widgets/CategorySales";
import DeviceShare from "Components/Widgets/DeviceShare";

import { ChartConfig } from "Constants/chart-config";

export default {
  components: {
    LineChartShadow,
    RecentSale,
    SupportRequest,
    Sales,
    ToDoList,
    Invoice,
    RecentOrders,
    CategorySale,
    LineChartWithArea,
    WeeklySales,
    Reviews,
    SocialFeeds,
    TopSelling,
    NewPost,
    BlogLayoutOne,
    BlogLayoutFour,
    DeviceShare
  },
  data() {
    return {
      blog: {
        id: 3,
        thumbnail: "/static/img/blog-3.jpg",
        title: "lorem ipsum is simply dummy text",
        body:
          "Consectetur adipisicing elit. Ullam expedita, necessitatibus sit exercitationem aut quo quos inventore similique nulla minima distinctio illo iste dignissimos vero nostrum, magni pariatur delectus natus.",
        date: "1-jun-2018"
      },
      ChartConfig,
      labels: ["A", "B", "C", "D", "E", "F", "J", "K", "L", "M", "N", "P"],
      totalEarnings: [30, 50, 25, 55, 44, 60, 30, 20, 40, 20, 40, 44],
      onlineRevenue: [30, 50, 25, 55, 44, 60, 30, 20, 40, 20, 40, 44],
      newCustomers: [30, 50, 25, 55, 44, 60, 30, 20, 40, 20, 40, 44]
    };
  }
};
</script>
