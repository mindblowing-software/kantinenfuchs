json.extract! order, :id, :ordered_at, :number, :customer_id, :user_id, :created_at, :updated_at
json.url order_url(order, format: :json)
