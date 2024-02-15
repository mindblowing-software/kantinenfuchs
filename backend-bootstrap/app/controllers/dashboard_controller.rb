class DashboardController < ApplicationController

  def show
    @today_orders = Order.where(ordered_at: Date.today)
    @today_sum = @today_orders.sum("number")
    @monthly_sum = Order.where("ordered_at >= ?", Date.today.beginning_of_month ).sum("number")
  end
end
