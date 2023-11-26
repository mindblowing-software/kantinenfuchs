class Customer < ApplicationRecord
  validates :name,  :presence => true
  validates :number_orders, :presence => true
end
